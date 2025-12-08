<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Artist;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Post extends Component
{
    use WithFileUploads;

    public $songFiles = [];
    public $uploadedSongs = [];
    public $coverFiles = []; // Separate array for cover uploads
    public $isUploading = false;
    public $isOpen = false;
    public $uploadError = null;
    public $uploadProgress = 0;
    public $selectedAlbumId = null; // Album selection for all songs
    
    // Searchable inputs
    public string $albumSearch = '';
    public array $albumSuggestions = [];
    public ?Album $selectedAlbum = null;

    protected $rules = [
        'songFiles.*' => 'file|max:102400|mimes:mp3,wav',
        'uploadedSongs.*.name' => 'required|string|max:255',
        'uploadedSongs.*.label' => 'nullable|string|max:255',
        'uploadedSongs.*.artist_search' => 'nullable|string|max:255',
        'uploadedSongs.*.genre' => 'nullable|string',
        'uploadedSongs.*.tags' => 'nullable|string',
        'coverFiles.*' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
    ];

    protected $messages = [
        'songFiles.*.mimes' => 'Song files must be MP3 or WAV format.',
        'songFiles.*.max' => 'Song file must be less than 100MB.',
        'coverFiles.*.image' => 'Cover must be an image file.',
        'coverFiles.*.mimes' => 'Cover must be JPG, PNG, GIF, or WebP.',
        'coverFiles.*.max' => 'Cover must be less than 5MB.',
    ];

    protected $listeners = ['fileUploaded' => 'handleFileUploaded'];

    /**
     * Search albums as user types
     */
    public function updatedAlbumSearch($value)
    {
        if (strlen($value) < 1) {
            $this->albumSuggestions = [];
            return;
        }

        $this->albumSuggestions = Album::where('title', 'like', '%' . $value . '%')
            ->orderBy('title')
            ->take(5)
            ->get()
            ->map(fn($album) => [
                'id' => $album->id,
                'title' => $album->title,
                'artist' => $album->artist_name,
                'cover' => $album->cover_url,
            ])
            ->toArray();
    }

    /**
     * Select an album from suggestions
     */
    public function selectAlbum(int $albumId)
    {
        $album = Album::find($albumId);
        if ($album) {
            $this->selectedAlbum = $album;
            $this->selectedAlbumId = $album->id;
            $this->albumSearch = $album->title;
            $this->albumSuggestions = [];
        }
    }

    /**
     * Clear selected album
     */
    public function clearAlbum()
    {
        $this->selectedAlbum = null;
        $this->selectedAlbumId = null;
        $this->albumSearch = '';
        $this->albumSuggestions = [];
    }

    /**
     * Search artists for a specific song index
     */
    public function searchArtists(int $index, string $query)
    {
        if (strlen($query) < 1) {
            $this->uploadedSongs[$index]['artist_suggestions'] = [];
            return;
        }

        $this->uploadedSongs[$index]['artist_suggestions'] = Artist::where('name', 'like', '%' . $query . '%')
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
     * Select an artist for a specific song
     */
    public function selectArtist(int $index, int $artistId)
    {
        $artist = Artist::find($artistId);
        if ($artist && isset($this->uploadedSongs[$index])) {
            // Add to selected artists array
            if (!isset($this->uploadedSongs[$index]['selected_artists'])) {
                $this->uploadedSongs[$index]['selected_artists'] = [];
            }
            
            // Check if not already selected
            $alreadySelected = collect($this->uploadedSongs[$index]['selected_artists'])
                ->contains('id', $artistId);
            
            if (!$alreadySelected) {
                $this->uploadedSongs[$index]['selected_artists'][] = [
                    'id' => $artist->id,
                    'name' => $artist->name,
                ];
            }
            
            $this->uploadedSongs[$index]['artist_search'] = '';
            $this->uploadedSongs[$index]['artist_suggestions'] = [];
        }
    }

    /**
     * Remove an artist from a song
     */
    public function removeArtist(int $songIndex, int $artistIndex)
    {
        if (isset($this->uploadedSongs[$songIndex]['selected_artists'][$artistIndex])) {
            unset($this->uploadedSongs[$songIndex]['selected_artists'][$artistIndex]);
            $this->uploadedSongs[$songIndex]['selected_artists'] = array_values(
                $this->uploadedSongs[$songIndex]['selected_artists']
            );
        }
    }

    /**
     * Handle cover file upload for specific song index
     */
    public function updatedCoverFiles($value, $key)
    {
        Log::info('Cover uploaded', ['key' => $key, 'hasFile' => !empty($value)]);
        
        // Key will be the index like "0", "1", etc.
        if (isset($this->uploadedSongs[$key])) {
            $this->uploadedSongs[$key]['has_cover'] = true;
        }
    }

    public function updatedSongFiles()
    {
        Log::info('updatedSongFiles called', [
            'count' => is_array($this->songFiles) ? count($this->songFiles) : 'not array',
            'type' => gettype($this->songFiles)
        ]);

        $this->uploadError = null;
        
        // Check if we have files
        if (empty($this->songFiles)) {
            Log::warning('No files received in updatedSongFiles');
            return;
        }

        try {
            $this->validate([
                'songFiles.*' => 'file|max:102400|mimes:mp3,wav',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->uploadError = 'File tidak valid. Pastikan file adalah .mp3 atau .wav dengan ukuran maksimal 100MB.';
            Log::error('Validation error: ' . json_encode($e->errors()));
            return;
        }

        $this->isUploading = true;
        
        foreach ($this->songFiles as $index => $file) {
            try {
                Log::info('Processing file', [
                    'index' => $index,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ]);
                $this->processUpload($file);
            } catch (\Exception $e) {
                $this->uploadError = 'Error saat upload: ' . $e->getMessage();
                Log::error('Upload error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->isUploading = false;
        $this->songFiles = [];
        
        Log::info('Upload completed', [
            'uploaded_count' => count($this->uploadedSongs)
        ]);
    }

    protected function processUpload($file)
    {
        $filename = $file->getClientOriginalName();
        $uniqueFilename = time() . '_' . $filename;
        
        // Try Azure first, fall back to local storage
        try {
            $path = $file->storeAs('uploads', $uniqueFilename, 'azure');
            $storageType = 'azure';
        } catch (\Exception $e) {
            Log::warning('Azure upload failed, using local storage: ' . $e->getMessage());
            // Fallback to local storage
            $path = $file->storeAs('uploads', $uniqueFilename, 'public');
            $storageType = 'local';
        }

        if (!$path) {
            throw new \Exception('Gagal menyimpan file ke storage');
        }

        Log::info('File uploaded successfully', [
            'path' => $path,
            'storage' => $storageType,
            'original_name' => $filename
        ]);

        $this->uploadedSongs[] = [
            'temp_path' => $path,
            'original_name' => $filename,
            'name' => pathinfo($filename, PATHINFO_FILENAME), 
            'label' => '',
            'genre' => '',
            'tags' => '',
            'has_cover' => false,
            'storage_type' => $storageType,
            'artist_search' => '',
            'artist_suggestions' => [],
            'selected_artists' => [],
        ];
    }

    public function removeSong($index)
    {
        if (isset($this->uploadedSongs[$index])) {
            $song = $this->uploadedSongs[$index];
            $disk = $song['storage_type'] ?? 'public';
            
            try {
                Storage::disk($disk)->delete($song['temp_path']);
            } catch (\Exception $e) {
                Log::warning('Failed to delete temp file: ' . $e->getMessage());
            }
        }
        
        // Also remove cover file if exists
        if (isset($this->coverFiles[$index])) {
            unset($this->coverFiles[$index]);
        }
        
        unset($this->uploadedSongs[$index]);
        $this->uploadedSongs = array_values($this->uploadedSongs);
        $this->coverFiles = array_values($this->coverFiles);
    }

    public function saveAllSongs()
    {
        $this->validate();
        $this->uploadError = null;

        try {
            foreach ($this->uploadedSongs as $index => $song) {
                $storageType = $song['storage_type'] ?? 'public';
                
                // ========== MOVE FILE FROM TEMP → FINAL ==========
                $finalAudioPath = 'songs/' . basename($song['temp_path']);
                Storage::disk($storageType)->copy($song['temp_path'], $finalAudioPath);
                Storage::disk($storageType)->delete($song['temp_path']);

                // ========== GET SONG DURATION ==========
                $duration = 0;
                try {
                    $tempLocal = storage_path('app/temp_audio_' . uniqid() . '.mp3');
                    file_put_contents($tempLocal, Storage::disk($storageType)->get($finalAudioPath));

                    if (class_exists(\wapmorgan\Mp3Info\Mp3Info::class)) {
                        $audio = new \wapmorgan\Mp3Info\Mp3Info($tempLocal);
                        $duration = (int) $audio->duration;
                    }

                    unlink($tempLocal);
                } catch (\Exception $e) {
                    Log::warning('Could not get song duration: ' . $e->getMessage());
                }

                // ========== UPLOAD COVER (OPTIONAL) ==========
                $coverPath = null;
                if (isset($this->coverFiles[$index]) && $this->coverFiles[$index]) {
                    try {
                        $coverPath = $this->coverFiles[$index]->store('covers', 'azure');
                    } catch (\Exception $e) {
                        Log::warning('Azure cover upload failed, using local: ' . $e->getMessage());
                        $coverPath = $this->coverFiles[$index]->store('covers', 'public');
                    }
                }

                // ========== SAVE TO DATABASE ==========
                $artistNames = [];
                $artistIds = [];
                
                if (!empty($song['selected_artists'])) {
                    foreach ($song['selected_artists'] as $selectedArtist) {
                        $artistNames[] = $selectedArtist['name'];
                        $artistIds[] = $selectedArtist['id'];
                    }
                }

                $newSong = \App\Models\Song::create([
                    'title'        => $song['name'],
                    'artist_name'  => !empty($artistNames) ? implode(', ', $artistNames) : ($song['label'] ?: auth()->user()->name ?? 'Unknown Artist'),
                    'album_id'     => $this->selectedAlbumId ?: null,
                    'cover'        => $coverPath,
                    'audio_path'   => $finalAudioPath,
                    'duration'     => $duration,
                    'play_count'   => 0,
                    'listeners'    => 0,
                    'save_count'   => 0,
                    'release_date' => now(),
                ]);

                // Attach artists to song via pivot table
                if (!empty($artistIds)) {
                    $newSong->artists()->attach($artistIds);
                }

                Log::info('Song saved to database', ['id' => $newSong->id, 'title' => $newSong->title]);
            }

            session()->flash('success', 'Songs successfully uploaded!');
            $this->uploadedSongs = [];
            $this->selectedAlbumId = null;
            $this->dispatch('close-upload-modal');

        } catch (\Exception $e) {
            $this->uploadError = 'Error saving songs: ' . $e->getMessage();
            Log::error('Save songs error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.admin.post', [
            'albums' => Album::orderBy('title')->get(),
        ]);
    }
}