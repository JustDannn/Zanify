<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AlbumManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Create Album Modal
    public $showCreateModal = false;
    public $createForm = [
        'title' => '',
        'artist_name' => '',
        'year' => '',
        'release_date' => '',
    ];
    public $createCover = null;
    public $selectedSongs = [];
    public $uploadError = null;
    
    // Artist search for create
    public string $createArtistSearch = '';
    public $createArtistSuggestions = [];
    public ?Artist $createSelectedArtist = null;

    // Edit Album Modal
    public $showEditModal = false;
    public $editingAlbum = null;
    public $editForm = [
        'title' => '',
        'artist_name' => '',
        'year' => '',
        'release_date' => '',
    ];
    public $editCover = null;
    public $editSelectedSongs = [];
    
    // Artist search for edit
    public string $editArtistSearch = '';
    public $editArtistSuggestions = [];
    public ?Artist $editSelectedArtist = null;

    // Delete Confirmation
    public $showDeleteModal = false;
    public $deletingAlbumId = null;
    public $deletingAlbumTitle = '';

    protected $rules = [
        'createForm.title' => 'required|string|max:255',
        'createForm.artist_name' => 'nullable|string|max:255',
        'createForm.year' => 'nullable|integer|min:1900|max:2100',
        'createForm.release_date' => 'nullable|date',
        'createCover' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        'editForm.title' => 'required|string|max:255',
        'editForm.artist_name' => 'nullable|string|max:255',
        'editForm.year' => 'nullable|integer|min:1900|max:2100',
        'editForm.release_date' => 'nullable|date',
        'editCover' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
    ];

    protected $messages = [
        'createCover.image' => 'Cover must be an image file.',
        'createCover.mimes' => 'Cover must be a JPG, PNG, GIF, or WebP image.',
        'createCover.max' => 'Cover image must be less than 5MB.',
        'editCover.image' => 'Cover must be an image file.',
        'editCover.mimes' => 'Cover must be a JPG, PNG, GIF, or WebP image.',
        'editCover.max' => 'Cover image must be less than 5MB.',
    ];

    /**
     * Handle createCover upload
     */
    public function updatedCreateCover()
    {
        $this->uploadError = null;
        
        try {
            $this->validateOnly('createCover');
            Log::info('Album cover uploaded successfully', [
                'name' => $this->createCover->getClientOriginalName(),
                'size' => $this->createCover->getSize(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->uploadError = $e->validator->errors()->first('createCover');
            $this->createCover = null;
            Log::error('Album cover validation failed: ' . $this->uploadError);
        }
    }

    /**
     * Handle editCover upload
     */
    public function updatedEditCover()
    {
        $this->uploadError = null;
        
        try {
            $this->validateOnly('editCover');
            Log::info('Album edit cover uploaded successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->uploadError = $e->validator->errors()->first('editCover');
            $this->editCover = null;
            Log::error('Album edit cover validation failed: ' . $this->uploadError);
        }
    }

    /**
     * Search artists for create form
     */
    public function updatedCreateArtistSearch($value)
    {
        if (strlen($value) < 1) {
            $this->createArtistSuggestions = [];
            return;
        }

        $this->createArtistSuggestions = Artist::where('name', 'like', '%' . $value . '%')
            ->orderBy('name')
            ->take(5)
            ->get();
    }

    /**
     * Select artist for create form
     */
    public function selectCreateArtist(int $artistId)
    {
        $artist = Artist::find($artistId);
        if ($artist) {
            $this->createSelectedArtist = $artist;
            $this->createForm['artist_name'] = $artist->name;
            $this->createArtistSearch = $artist->name;
            $this->createArtistSuggestions = [];
        }
    }

    /**
     * Clear selected artist for create
     */
    public function clearCreateArtist()
    {
        $this->createSelectedArtist = null;
        $this->createForm['artist_name'] = '';
        $this->createArtistSearch = '';
        $this->createArtistSuggestions = [];
    }

    /**
     * Search artists for edit form
     */
    public function updatedEditArtistSearch($value)
    {
        if (strlen($value) < 1) {
            $this->editArtistSuggestions = [];
            return;
        }

        $this->editArtistSuggestions = Artist::where('name', 'like', '%' . $value . '%')
            ->orderBy('name')
            ->take(5)
            ->get();
    }

    /**
     * Select artist for edit form
     */
    public function selectEditArtist(int $artistId)
    {
        $artist = Artist::find($artistId);
        if ($artist) {
            $this->editSelectedArtist = $artist;
            $this->editForm['artist_name'] = $artist->name;
            $this->editArtistSearch = $artist->name;
            $this->editArtistSuggestions = [];
        }
    }

    /**
     * Clear selected artist for edit
     */
    public function clearEditArtist()
    {
        $this->editSelectedArtist = null;
        $this->editForm['artist_name'] = '';
        $this->editArtistSearch = '';
        $this->editArtistSuggestions = [];
    }

    /**
     * Open create album modal
     */
    public function openCreateModal()
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    /**
     * Reset create form
     */
    public function resetCreateForm()
    {
        $this->createForm = [
            'title' => '',
            'artist_name' => '',
            'year' => date('Y'),
            'release_date' => '',
        ];
        $this->createCover = null;
        $this->selectedSongs = [];
        $this->uploadError = null;
        $this->createArtistSearch = '';
        $this->createArtistSuggestions = [];
        $this->createSelectedArtist = null;
    }

    /**
     * Close create modal
     */
    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    /**
     * Create new album
     */
    public function createAlbum()
    {
        $this->validate([
            'createForm.title' => 'required|string|max:255',
            'createForm.artist_name' => 'nullable|string|max:255',
            'createForm.year' => 'nullable|integer|min:1900|max:2100',
            'createForm.release_date' => 'nullable|date',
            'createCover' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        try {
            // Handle artist - use selected artist or create new one
            $artistId = null;
            if ($this->createSelectedArtist) {
                $artistId = $this->createSelectedArtist->id;
            } elseif ($this->createForm['artist_name']) {
                $artist = Artist::firstOrCreate(['name' => $this->createForm['artist_name']]);
                $artistId = $artist->id;
            }

            // Handle cover upload
            $coverPath = null;
            if ($this->createCover) {
                try {
                    $coverPath = $this->createCover->store('album-covers', 'azure');
                } catch (\Exception $e) {
                    Log::warning('Azure cover upload failed, using local: ' . $e->getMessage());
                    $coverPath = $this->createCover->store('album-covers', 'public');
                }
            }

            // Create album
            $album = Album::create([
                'title' => $this->createForm['title'],
                'artist_id' => $artistId,
                'cover' => $coverPath,
                'year' => $this->createForm['year'] ?: null,
                'release_date' => $this->createForm['release_date'] ?: null,
            ]);

            // Assign selected songs to album
            if (!empty($this->selectedSongs)) {
                Song::whereIn('id', $this->selectedSongs)->update(['album_id' => $album->id]);
            }

            session()->flash('success', 'Album created successfully!');
            $this->closeCreateModal();
            $this->dispatch('albumCreated');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create album: ' . $e->getMessage());
            Log::error('Create album error: ' . $e->getMessage());
        }
    }

    /**
     * Open edit modal for an album
     */
    public function editAlbum($albumId)
    {
        $album = Album::with(['songs', 'artist'])->find($albumId);
        if (!$album) return;

        $this->editingAlbum = $album;
        $this->editForm = [
            'title' => $album->title,
            'artist_name' => $album->artist?->name ?? '',
            'year' => $album->year,
            'release_date' => $album->release_date?->format('Y-m-d'),
        ];
        $this->editCover = null;
        $this->editSelectedSongs = $album->songs->pluck('id')->toArray();
        
        // Set artist search state
        if ($album->artist) {
            $this->editSelectedArtist = $album->artist;
            $this->editArtistSearch = $album->artist->name;
        } else {
            $this->editSelectedArtist = null;
            $this->editArtistSearch = '';
        }
        $this->editArtistSuggestions = [];
        
        $this->showEditModal = true;
    }

    /**
     * Update album
     */
    public function updateAlbum()
    {
        $this->validate([
            'editForm.title' => 'required|string|max:255',
            'editForm.artist_name' => 'nullable|string|max:255',
            'editForm.year' => 'nullable|integer|min:1900|max:2100',
            'editForm.release_date' => 'nullable|date',
            'editCover' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        if (!$this->editingAlbum) return;

        try {
            // Handle artist - use selected artist or create new one
            $artistId = null;
            if ($this->editSelectedArtist) {
                $artistId = $this->editSelectedArtist->id;
            } elseif ($this->editForm['artist_name']) {
                $artist = Artist::firstOrCreate(['name' => $this->editForm['artist_name']]);
                $artistId = $artist->id;
            }

            $updateData = [
                'title' => $this->editForm['title'],
                'artist_id' => $artistId,
                'year' => $this->editForm['year'] ?: null,
                'release_date' => $this->editForm['release_date'] ?: null,
            ];

            // Handle cover upload
            if ($this->editCover) {
                // Delete old cover
                if ($this->editingAlbum->cover) {
                    try {
                        Storage::disk('azure')->delete($this->editingAlbum->cover);
                    } catch (\Exception $e) {
                        try {
                            Storage::disk('public')->delete($this->editingAlbum->cover);
                        } catch (\Exception $e2) {
                            Log::warning('Could not delete old album cover: ' . $e2->getMessage());
                        }
                    }
                }

                // Upload new cover
                try {
                    $coverPath = $this->editCover->store('album-covers', 'azure');
                } catch (\Exception $e) {
                    $coverPath = $this->editCover->store('album-covers', 'public');
                }
                $updateData['cover'] = $coverPath;
            }

            $this->editingAlbum->update($updateData);

            // Update song assignments - first remove all songs from this album
            Song::where('album_id', $this->editingAlbum->id)->update(['album_id' => null]);
            
            // Then assign selected songs
            if (!empty($this->editSelectedSongs)) {
                Song::whereIn('id', $this->editSelectedSongs)->update(['album_id' => $this->editingAlbum->id]);
            }

            session()->flash('success', 'Album updated successfully!');
            $this->closeEditModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update album: ' . $e->getMessage());
            Log::error('Update album error: ' . $e->getMessage());
        }
    }

    /**
     * Close edit modal
     */
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingAlbum = null;
        $this->editCover = null;
        $this->editForm = ['title' => '', 'artist_name' => '', 'year' => '', 'release_date' => ''];
        $this->editSelectedSongs = [];
    }

    /**
     * Confirm delete album
     */
    public function confirmDelete($albumId)
    {
        $album = Album::find($albumId);
        if (!$album) return;

        $this->deletingAlbumId = $albumId;
        $this->deletingAlbumTitle = $album->title;
        $this->showDeleteModal = true;
    }

    /**
     * Delete album
     */
    public function deleteAlbum()
    {
        if (!$this->deletingAlbumId) return;

        try {
            $album = Album::find($this->deletingAlbumId);
            
            if ($album) {
                // Delete cover image
                if ($album->cover) {
                    try {
                        if (Storage::disk('azure')->exists($album->cover)) {
                            Storage::disk('azure')->delete($album->cover);
                        } elseif (Storage::disk('public')->exists($album->cover)) {
                            Storage::disk('public')->delete($album->cover);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not delete album cover: ' . $e->getMessage());
                    }
                }

                // Remove album association from songs (don't delete songs)
                Song::where('album_id', $album->id)->update(['album_id' => null]);
                
                // Delete album record
                $album->delete();

                session()->flash('success', 'Album deleted successfully!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete album: ' . $e->getMessage());
            Log::error('Delete album error: ' . $e->getMessage());
        }

        $this->closeDeleteModal();
    }

    /**
     * Close delete modal
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deletingAlbumId = null;
        $this->deletingAlbumTitle = '';
    }

    /**
     * Get available songs (not assigned to any album or assigned to current editing album)
     */
    public function getAvailableSongsProperty()
    {
        $query = Song::query();
        
        if ($this->editingAlbum) {
            // Include songs from current album + unassigned songs
            $query->where(function($q) {
                $q->whereNull('album_id')
                  ->orWhere('album_id', $this->editingAlbum->id);
            });
        } else {
            // Only unassigned songs for creating new album
            $query->whereNull('album_id');
        }
        
        return $query->orderBy('title')->get();
    }

    /**
     * Get all songs for selection
     */
    public function getAllSongsProperty()
    {
        return Song::orderBy('title')->get();
    }

    public function render()
    {
        return view('livewire.admin.album-manager', [
            'albums' => Album::with(['artist', 'songs'])->latest()->paginate(20),
            'availableSongs' => $this->availableSongs,
            'allSongs' => $this->allSongs,
        ]);
    }
}
