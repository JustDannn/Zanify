<?php

namespace App\Livewire\Admin;

use App\Models\Song;
use App\Models\Artist;
use App\Models\Album;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminDashboard extends Component
{
    use WithFileUploads;

    // Edit Modal
    public $showEditModal = false;
    public $editingSong = null;
    public $editForm = [
        'title' => '',
        'artist_name' => '',
        'release_date' => '',
        'album_id' => null,
    ];
    public $editCover = null; // Separate property for cover upload

    // Delete Confirmation
    public $showDeleteModal = false;
    public $deletingSongId = null;
    public $deletingSongTitle = '';

    protected $rules = [
        'editForm.title' => 'required|string|max:255',
        'editForm.artist_name' => 'nullable|string|max:255',
        'editForm.release_date' => 'nullable|date',
        'editForm.album_id' => 'nullable|exists:albums,id',
        'editCover' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
    ];

    protected $messages = [
        'editCover.image' => 'Cover must be an image file.',
        'editCover.mimes' => 'Cover must be a JPG, PNG, GIF, or WebP image.',
        'editCover.max' => 'Cover image must be less than 5MB.',
    ];

    protected $listeners = ['songUploaded' => '$refresh'];

    /**
     * Open edit modal for a song
     */
    public function editSong($songId)
    {
        $song = Song::find($songId);
        if (!$song) return;

        $this->editingSong = $song;
        $this->editForm = [
            'title' => $song->title,
            'artist_name' => $song->artist_display,
            'release_date' => $song->release_date?->format('Y-m-d'),
            'album_id' => $song->album_id,
        ];
        $this->editCover = null;
        $this->showEditModal = true;
    }

    /**
     * Save edited song
     */
    public function updateSong()
    {
        $this->validate();

        if (!$this->editingSong) return;

        try {
            $updateData = [
                'title' => $this->editForm['title'],
                'artist_name' => $this->editForm['artist_name'],
                'release_date' => $this->editForm['release_date'] ?: null,
                'album_id' => $this->editForm['album_id'] ?: null,
            ];

            // Handle cover upload
            if ($this->editCover) {
                // Delete old cover
                if ($this->editingSong->cover) {
                    try {
                        Storage::disk('azure')->delete($this->editingSong->cover);
                    } catch (\Exception $e) {
                        try {
                            Storage::disk('public')->delete($this->editingSong->cover);
                        } catch (\Exception $e2) {
                            Log::warning('Could not delete old cover: ' . $e2->getMessage());
                        }
                    }
                }

                // Upload new cover
                try {
                    $coverPath = $this->editCover->store('covers', 'azure');
                } catch (\Exception $e) {
                    $coverPath = $this->editCover->store('covers', 'public');
                }
                $updateData['cover'] = $coverPath;
            }

            $this->editingSong->update($updateData);

            // Handle artist relation (create if not exists)
            if ($this->editForm['artist_name']) {
                $artistNames = array_map('trim', explode(',', $this->editForm['artist_name']));
                $artistIds = [];
                
                foreach ($artistNames as $name) {
                    $artist = Artist::firstOrCreate(['name' => $name]);
                    $artistIds[] = $artist->id;
                }
                
                $this->editingSong->artists()->sync($artistIds);
            }

            session()->flash('success', 'Song updated successfully!');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update song: ' . $e->getMessage());
            Log::error('Update song error: ' . $e->getMessage());
        }
    }

    /**
     * Close edit modal
     */
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingSong = null;
        $this->editCover = null;
        $this->editForm = ['title' => '', 'artist_name' => '', 'release_date' => '', 'album_id' => null];
    }

    /**
     * Confirm delete song
     */
    public function confirmDelete($songId)
    {
        $song = Song::find($songId);
        if (!$song) return;

        $this->deletingSongId = $songId;
        $this->deletingSongTitle = $song->title;
        $this->showDeleteModal = true;
    }

    /**
     * Delete song
     */
    public function deleteSong()
    {
        if (!$this->deletingSongId) return;

        try {
            $song = Song::find($this->deletingSongId);
            
            if ($song) {
                // Delete audio file from storage
                if ($song->audio_path) {
                    try {
                        // Try Azure first
                        if (Storage::disk('azure')->exists($song->audio_path)) {
                            Storage::disk('azure')->delete($song->audio_path);
                        } elseif (Storage::disk('public')->exists($song->audio_path)) {
                            Storage::disk('public')->delete($song->audio_path);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not delete audio file: ' . $e->getMessage());
                    }
                }

                // Delete cover image
                if ($song->cover) {
                    try {
                        if (Storage::disk('azure')->exists($song->cover)) {
                            Storage::disk('azure')->delete($song->cover);
                        } elseif (Storage::disk('public')->exists($song->cover)) {
                            Storage::disk('public')->delete($song->cover);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not delete cover file: ' . $e->getMessage());
                    }
                }

                // Detach artists
                $song->artists()->detach();
                
                // Delete song record
                $song->delete();

                session()->flash('success', 'Song deleted successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete song: ' . $e->getMessage());
            Log::error('Delete song error: ' . $e->getMessage());
        }

        $this->closeDeleteModal();
    }

    /**
     * Close delete modal
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingSongId = null;
        $this->deletingSongTitle = '';
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'songs' => Song::with(['artists', 'album'])->latest()->get(),
            'albums' => Album::orderBy('title')->get(),
        ]);
    }
}
