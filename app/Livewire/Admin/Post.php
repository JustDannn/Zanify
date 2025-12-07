<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Post extends Component
{
    use WithFileUploads;

    public $songFiles = [];
    public $uploadedSongs = [];
    public $isUploading = false;
    public $isOpen = false;
    public $uploadError = null;
    public $uploadProgress = 0;

    protected $rules = [
        'songFiles.*' => 'file|max:102400|mimes:mp3,wav',
        'uploadedSongs.*.name' => 'required|string|max:255',
        'uploadedSongs.*.label' => 'nullable|string|max:255',
        'uploadedSongs.*.genre' => 'nullable|string',
        'uploadedSongs.*.tags' => 'nullable|string',
        'uploadedSongs.*.cover' => 'nullable|image|max:5120',
    ];

    protected $listeners = ['fileUploaded' => 'handleFileUploaded'];

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
            'cover' => null,
            'storage_type' => $storageType,
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
        
        unset($this->uploadedSongs[$index]);
        $this->uploadedSongs = array_values($this->uploadedSongs);
    }

    public function saveAllSongs()
    {
        $this->validate();
        $this->uploadError = null;

        try {
            foreach ($this->uploadedSongs as $song) {
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
                if (!empty($song['cover']) && is_object($song['cover'])) {
                    $coverPath = $song['cover']->store('covers', $storageType);
                }

                // ========== SAVE TO DATABASE ==========
                $newSong = \App\Models\Song::create([
                    'title'      => $song['name'],
                    'album_id'   => null,
                    'cover'      => $coverPath,
                    'audio_path' => $finalAudioPath,
                    'duration'   => $duration,
                ]);

                Log::info('Song saved to database', ['id' => $newSong->id, 'title' => $newSong->title]);
            }

            session()->flash('success', 'Songs successfully uploaded!');
            $this->uploadedSongs = [];
            $this->dispatch('close-upload-modal');

        } catch (\Exception $e) {
            $this->uploadError = 'Error saving songs: ' . $e->getMessage();
            Log::error('Save songs error: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.admin.post');
    }
}