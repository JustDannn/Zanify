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
    
    // Artist search for edit modal
    public string $editArtistSearch = '';
    public $editArtistSuggestions = [];
    public array $editSelectedArtists = [];
    
    // Album search for edit modal
    public string $editAlbumSearch = '';
    public $editAlbumSuggestions = [];
    public ?Album $editSelectedAlbum = null;

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
     * Search artists for edit modal
     */
    public function searchEditArtists(string $query)
    {
        if (strlen($query) < 1) {
            $this->editArtistSuggestions = [];
            return;
        }

        $this->editArtistSuggestions = Artist::where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->take(5)
            ->get()
            ->map(fn($artist) => [
                'id' => $artist->id,
                'name' => $artist->name,
                'photo' => $artist->photo_url,
            ])
            ->toArray();
    }

    /**
     * Select an artist for edit modal
     */
    public function selectEditArtist(int $artistId)
    {
        $artist = Artist::find($artistId);
        if ($artist) {
            // Check if not already selected
            $alreadySelected = collect($this->editSelectedArtists)->contains('id', $artistId);
            
            if (!$alreadySelected) {
                $this->editSelectedArtists[] = [
                    'id' => $artist->id,
                    'name' => $artist->name,
                ];
            }
            
            $this->editArtistSearch = '';
            $this->editArtistSuggestions = [];
        }
    }

    /**
     * Remove an artist from edit selection
     */
    public function removeEditArtist(int $index)
    {
        if (isset($this->editSelectedArtists[$index])) {
            unset($this->editSelectedArtists[$index]);
            $this->editSelectedArtists = array_values($this->editSelectedArtists);
        }
    }

    /**
     * Search albums for edit modal
     */
    public function searchEditAlbums(string $query)
    {
        if (strlen($query) < 1) {
            $this->editAlbumSuggestions = [];
            return;
        }

        $this->editAlbumSuggestions = Album::where('title', 'like', '%' . $query . '%')
            ->orderBy('title')
            ->take(5)
            ->get();
    }

    /**
     * Select album for edit modal
     */
    public function selectEditAlbum(int $albumId)
    {
        $album = Album::find($albumId);
        if ($album) {
            $this->editSelectedAlbum = $album;
            $this->editForm['album_id'] = $album->id;
            $this->editAlbumSearch = '';
            $this->editAlbumSuggestions = [];
        }
    }

    /**
     * Clear selected album in edit modal
     */
    public function clearEditAlbum()
    {
        $this->editSelectedAlbum = null;
        $this->editForm['album_id'] = null;
        $this->editAlbumSearch = '';
        $this->editAlbumSuggestions = [];
    }

    /**
     * Open edit modal for a song
     */
    public function editSong($songId)
    {
        $song = Song::with(['artists', 'album'])->find($songId);
        if (!$song) return;

        $this->editingSong = $song;
        $this->editForm = [
            'title' => $song->title,
            'artist_name' => $song->artist_display,
            'release_date' => $song->release_date?->format('Y-m-d'),
            'album_id' => $song->album_id,
        ];
        $this->editCover = null;
        
        // Load selected artists
        $this->editSelectedArtists = $song->artists->map(fn($artist) => [
            'id' => $artist->id,
            'name' => $artist->name,
        ])->toArray();
        $this->editArtistSearch = '';
        $this->editArtistSuggestions = [];
        
        // Load selected album
        if ($song->album) {
            $this->editSelectedAlbum = $song->album;
            $this->editAlbumSearch = '';
        } else {
            $this->editSelectedAlbum = null;
            $this->editAlbumSearch = '';
        }
        $this->editAlbumSuggestions = [];
        
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
            // Build artist_name from selected artists for display purposes
            $artistNameDisplay = collect($this->editSelectedArtists)->pluck('name')->implode(', ');
            
            $updateData = [
                'title' => $this->editForm['title'],
                'artist_name' => $artistNameDisplay,
                'release_date' => $this->editForm['release_date'] ?: null,
                'album_id' => $this->editSelectedAlbum?->id,
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

            // Sync artist relations from selected artists
            if (!empty($this->editSelectedArtists)) {
                $artistIds = collect($this->editSelectedArtists)->pluck('id')->toArray();
                $this->editingSong->artists()->sync($artistIds);
            } else {
                $this->editingSong->artists()->detach();
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
        
        // Reset artist search
        $this->editArtistSearch = '';
        $this->editArtistSuggestions = [];
        $this->editSelectedArtists = [];
        
        // Reset album search
        $this->editAlbumSearch = '';
        $this->editAlbumSuggestions = [];
        $this->editSelectedAlbum = null;
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
