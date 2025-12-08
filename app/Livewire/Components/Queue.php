<?php

namespace App\Livewire\Components;

use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Queue extends Component
{
    public array $queue = [];
    public array $history = []; // Track previously played songs
    public bool $isOpen = false;
    public ?int $currentSongId = null;

    #[On('toggle-queue')]
    public function toggleQueue()
    {
        $this->isOpen = !$this->isOpen;
    }

    #[On('add-to-queue')]
    public function addToQueue(int $songId)
    {
        $song = Song::with('artists')->find($songId);
        
        if (!$song) return;

        // Check if song already in queue
        $exists = collect($this->queue)->contains('id', $songId);
        if ($exists) {
            $this->dispatch('notify', message: "'{$song->title}' is already in queue");
            return;
        }

        $this->queue[] = [
            'id' => $song->id,
            'title' => $song->title,
            'artist' => $song->artist_display,
            'cover' => $song->cover_url,
            'duration_formatted' => $song->duration_formatted,
        ];

        // Show notification
        $this->dispatch('notify', message: "Added '{$song->title}' to queue!");
    }

    #[On('play-song')]
    public function onPlaySong(int $songId)
    {
        // Add current song to history before changing
        if ($this->currentSongId && $this->currentSongId !== $songId) {
            $currentSong = Song::find($this->currentSongId);
            if ($currentSong) {
                // Add to beginning of history
                array_unshift($this->history, [
                    'id' => $currentSong->id,
                    'title' => $currentSong->title,
                    'artist' => $currentSong->artist_display,
                    'cover' => $currentSong->cover_url,
                    'duration_formatted' => $currentSong->duration_formatted,
                ]);
                // Keep only last 20 songs in history
                $this->history = array_slice($this->history, 0, 20);
            }
        }
        
        $this->currentSongId = $songId;
    }

    public function playFromQueue(int $index)
    {
        if (!isset($this->queue[$index])) return;

        $song = $this->queue[$index];
        $this->dispatch('play-song', songId: $song['id']);
        
        // Remove from queue
        array_splice($this->queue, $index, 1);
    }

    public function removeFromQueue(int $index)
    {
        if (isset($this->queue[$index])) {
            array_splice($this->queue, $index, 1);
        }
    }

    public function clearQueue()
    {
        $this->queue = [];
    }

    public function moveUp(int $index)
    {
        if ($index > 0 && isset($this->queue[$index])) {
            $temp = $this->queue[$index - 1];
            $this->queue[$index - 1] = $this->queue[$index];
            $this->queue[$index] = $temp;
        }
    }

    public function moveDown(int $index)
    {
        if ($index < count($this->queue) - 1 && isset($this->queue[$index])) {
            $temp = $this->queue[$index + 1];
            $this->queue[$index + 1] = $this->queue[$index];
            $this->queue[$index] = $temp;
        }
    }

    /**
     * Play next song from queue
     */
    #[On('request-next-song')]
    public function playNextFromQueue()
    {
        if (count($this->queue) > 0) {
            $nextSong = array_shift($this->queue);
            $this->dispatch('play-song', songId: $nextSong['id']);
        }
    }

    /**
     * Play previous song from history
     */
    #[On('request-previous-song')]
    public function playPreviousFromHistory()
    {
        if (count($this->history) > 0) {
            $prevSong = array_shift($this->history);
            
            // Add current song back to front of queue
            if ($this->currentSongId) {
                $currentSong = Song::find($this->currentSongId);
                if ($currentSong) {
                    array_unshift($this->queue, [
                        'id' => $currentSong->id,
                        'title' => $currentSong->title,
                        'artist' => $currentSong->artist_display,
                        'cover' => $currentSong->cover_url,
                        'duration_formatted' => $currentSong->duration_formatted,
                    ]);
                }
            }
            
            // Play the previous song (bypass normal play-song to avoid adding to history)
            $this->currentSongId = $prevSong['id'];
            $song = Song::find($prevSong['id']);
            if ($song) {
                $this->dispatch('play-previous-song', songId: $prevSong['id']);
            }
        }
    }

    public function render()
    {
        return view('livewire.components.queue');
    }
}
