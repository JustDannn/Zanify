<?php

namespace App\Livewire\Components;

use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Queue extends Component
{
    public array $queue = []; // Manual queue (priority)
    public array $sourceSongs = []; // Autoplay from source
    public string $sourceName = ''; // e.g., "Liked Songs", "Search Results"
    public array $history = []; // Track previously played songs
    public bool $isOpen = false;
    public ?int $currentSongId = null;
    public ?array $currentSong = null;

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

    /**
     * Set the source playlist for autoplay
     */
    #[On('set-play-source')]
    public function setPlaySource(string $sourceName, array $songIds, int $startFromSongId)
    {
        $this->sourceName = $sourceName;
        
        // Find the index of the current song in the list
        $startIndex = array_search($startFromSongId, $songIds);
        if ($startIndex === false) $startIndex = 0;
        
        // Get songs after the current one for autoplay
        $remainingSongIds = array_slice($songIds, $startIndex + 1);
        
        // Load songs for source
        $this->sourceSongs = [];
        foreach ($remainingSongIds as $songId) {
            $song = Song::with('artists')->find($songId);
            if ($song) {
                $this->sourceSongs[] = [
                    'id' => $song->id,
                    'title' => $song->title,
                    'artist' => $song->artist_display,
                    'cover' => $song->cover_url,
                    'duration_formatted' => $song->duration_formatted,
                ];
            }
        }
    }

    #[On('play-song')]
    public function onPlaySong(int $songId)
    {
        // Add current song to history before changing
        if ($this->currentSongId && $this->currentSongId !== $songId) {
            if ($this->currentSong) {
                // Add to beginning of history
                array_unshift($this->history, $this->currentSong);
                // Keep only last 20 songs in history
                $this->history = array_slice($this->history, 0, 20);
            }
        }
        
        $this->currentSongId = $songId;
        
        // Load current song info
        $song = Song::with('artists')->find($songId);
        if ($song) {
            $this->currentSong = [
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist_display,
                'cover' => $song->cover_url,
                'duration_formatted' => $song->duration_formatted,
            ];
        }
        
        // Remove from source songs if it exists there
        $this->sourceSongs = array_values(array_filter($this->sourceSongs, fn($s) => $s['id'] !== $songId));
    }

    public function playFromQueue(int $index)
    {
        if (!isset($this->queue[$index])) return;

        $song = $this->queue[$index];
        $this->dispatch('play-song', songId: $song['id']);
        
        // Remove from queue
        array_splice($this->queue, $index, 1);
    }

    public function playFromSource(int $index)
    {
        if (!isset($this->sourceSongs[$index])) return;

        $song = $this->sourceSongs[$index];
        $this->dispatch('play-song', songId: $song['id']);
        
        // Remove songs before this one from source (they become "skipped")
        $this->sourceSongs = array_slice($this->sourceSongs, $index + 1);
    }

    public function removeFromQueue(int $index)
    {
        if (isset($this->queue[$index])) {
            array_splice($this->queue, $index, 1);
        }
    }

    public function removeFromSource(int $index)
    {
        if (isset($this->sourceSongs[$index])) {
            array_splice($this->sourceSongs, $index, 1);
            $this->sourceSongs = array_values($this->sourceSongs);
        }
    }

    public function clearQueue()
    {
        $this->queue = [];
    }

    public function clearSource()
    {
        $this->sourceSongs = [];
        $this->sourceName = '';
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
     * Play next song - priority: manual queue first, then source
     */
    #[On('request-next-song')]
    public function playNextFromQueue()
    {
        // First, check manual queue (priority)
        if (count($this->queue) > 0) {
            $nextSong = array_shift($this->queue);
            $this->dispatch('play-song', songId: $nextSong['id']);
            return;
        }
        
        // Then, check source songs (autoplay from playlist)
        if (count($this->sourceSongs) > 0) {
            $nextSong = array_shift($this->sourceSongs);
            $this->dispatch('play-song', songId: $nextSong['id']);
            return;
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
            
            // Add current song back to front of source songs
            if ($this->currentSong) {
                array_unshift($this->sourceSongs, $this->currentSong);
            }
            
            // Play the previous song (bypass normal play-song to avoid adding to history)
            $this->currentSongId = $prevSong['id'];
            $this->currentSong = $prevSong;
            
            $this->dispatch('play-previous-song', songId: $prevSong['id']);
        }
    }

    public function render()
    {
        return view('livewire.components.queue');
    }
}
