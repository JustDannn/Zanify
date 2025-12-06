<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;

class Post extends Component
{
    use WithFileUploads;

    public $songFiles = [];
    public $uploadedSongs = [];
    public $isUploading = false;
    public $isOpen = false;

    protected $rules = [
        'songFiles.*' => 'file|max:102400|mimes:mp3,wav',
        'uploadedSongs.*.name' => 'required|string|max:255',
        'uploadedSongs.*.label' => 'nullable|string|max:255',
        'uploadedSongs.*.genre' => 'nullable|string',
        'uploadedSongs.*.tags' => 'nullable|string',
        'uploadedSongs.*.cover' => 'nullable|image|max:5120',
    ];
    public function updatedSongFiles()
    {
        $this->validate([
            'songFiles.*' => 'file|max:102400|mimes:mp3,wav',
        ]);

        $this->isUploading = true;
        
        foreach ($this->songFiles as $file) {
            $this->processUpload($file);
        }

        $this->isUploading = false;
        $this->songFiles = [];
    }

    protected function processUpload($file)
    {
        $filename = $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename, 'azure');

        $this->uploadedSongs[] = [
            'temp_path' => $path,
            'original_name' => $filename,
            'name' => str_replace(['.mp3', '.wav'], '', $filename), 
            'label' => '',
            'genre' => '',
            'tags' => '',
            'cover' => null,
        ];
    }

    public function removeSong($index)
    {
        // Optional: Delete from Azure storage if needed
        // Storage::disk('azure')->delete($this->uploadedSongs[$index]['temp_path']);
        
        unset($this->uploadedSongs[$index]);
        $this->uploadedSongs = array_values($this->uploadedSongs);
    }

    public function saveAllSongs()
    {
        $this->validate();

        try {
            foreach ($this->uploadedSongs as $song) {
                // Save to database or process further
                // Example:
                // Song::create([
                //     'name' => $song['name'],
                //     'label' => $song['label'],
                //     'genre' => $song['genre'],
                //     'tags' => $song['tags'],
                //     'file_path' => $song['temp_path'],
                //     'cover_path' => $song['cover'] ? $song['cover']->store('covers', 'azure') : null,
                // ]);
            }

            session()->flash('success', count($this->uploadedSongs) . ' songs saved successfully!');
            
            // Reset the form
            $this->uploadedSongs = [];
            $this->dispatch('close-upload-modal');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving songs: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.post');
    }
}