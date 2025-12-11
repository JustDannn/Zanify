<?php

namespace App\Livewire\Components;

use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

/**
 * Queue Component using Doubly Linked List concept
 * 
 * Structure: history <-> currentSong <-> queue/sourceSongs
 * - prev pointer: history array (most recent first)
 * - next pointer: queue (manual) then sourceSongs (autoplay)
 * - Circular: when repeat mode is 'all', loops back to beginning
 */
class Queue extends Component
{
    public array $queue = []; // Manual queue (priority) - "next" nodes
    public array $sourceSongs = []; // Autoplay from source - "next" nodes after queue
    public array $originalSource = []; // Original source for circular repeat
    public string $sourceName = ''; // e.g., "Liked Songs", "Search Results"
    public array $history = []; // "prev" nodes - Track previously played songs
    public bool $isOpen = false;
    public ?int $currentSongId = null;
    public ?array $currentSong = null;
    
    // Repeat mode: 'off', 'all' (circular), 'one' (single song)
    public string $repeatMode = 'off';

    #[On('toggle-queue')]
    public function toggleQueue()
    {
        $this->isOpen = !$this->isOpen;
    }

    /**
     * Cycle through repeat modes: off -> all -> one -> off
     */
    #[On('toggle-repeat')]
    public function toggleRepeat()
    {
        $modes = ['off', 'all', 'one'];
        $currentIndex = array_search($this->repeatMode, $modes);
        $nextIndex = ($currentIndex + 1) % count($modes);
        $this->repeatMode = $modes[$nextIndex];
        
        // Notify Alpine.js about the change
        $this->dispatch('repeat-mode-changed', mode: $this->repeatMode);
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
     * Also stores original source for circular repeat
     */
    #[On('set-play-source')]
    public function setPlaySource(string $sourceName, array $songIds, int $startFromSongId)
    {
        $this->sourceName = $sourceName;
        
        // Store all songs for circular repeat (originalSource)
        $this->originalSource = [];
        foreach ($songIds as $songId) {
            $song = Song::with('artists')->find($songId);
            if ($song) {
                $this->originalSource[] = [
                    'id' => $song->id,
                    'title' => $song->title,
                    'artist' => $song->artist_display,
                    'cover' => $song->cover_url,
                    'duration_formatted' => $song->duration_formatted,
                ];
            }
        }
        
        // Find the index of the current song in the list
        $startIndex = array_search($startFromSongId, $songIds);
        if ($startIndex === false) $startIndex = 0;
        
        // Get songs after the current one for autoplay
        $remainingSongIds = array_slice($songIds, $startIndex + 1);
        
        // Load songs for source (next nodes)
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
        
        // Clear history when starting new source (new playlist = fresh start)
        $this->history = [];
    }

    #[On('play-song')]
    public function onPlaySong(int $songId)
    {
        // Add current song to history (prev pointer in doubly linked list)
        if ($this->currentSongId && $this->currentSongId !== $songId) {
            if ($this->currentSong) {
                // Add to beginning of history (most recent first)
                array_unshift($this->history, $this->currentSong);
                // Keep only last 50 songs in history for memory efficiency
                $this->history = array_slice($this->history, 0, 50);
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
        
        // Remove from source songs if it exists there (advance pointer)
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
     * Play next song using doubly linked list traversal
     * Priority: manual queue -> source songs -> circular repeat (if enabled)
     */
    #[On('request-next-song')]
    public function playNextFromQueue()
    {
        // First, check manual queue (priority - these are inserted nodes)
        if (count($this->queue) > 0) {
            $nextSong = array_shift($this->queue);
            $this->dispatch('play-song', songId: $nextSong['id']);
            return;
        }
        
        // Then, check source songs (autoplay from playlist - natural next nodes)
        if (count($this->sourceSongs) > 0) {
            $nextSong = array_shift($this->sourceSongs);
            $this->dispatch('play-song', songId: $nextSong['id']);
            return;
        }
        
        // If repeat mode is 'all', implement circular doubly linked list
        // Go back to the beginning of the original source
        if ($this->repeatMode === 'all' && count($this->originalSource) > 0) {
            // Reset source songs to original (circular - tail connects to head)
            $this->sourceSongs = $this->originalSource;
            $this->history = []; // Clear history for fresh circular loop
            
            // Play the first song
            $nextSong = array_shift($this->sourceSongs);
            $this->dispatch('play-song', songId: $nextSong['id']);
            return;
        }
        
        // No more songs and repeat is off - stop playback
        $this->dispatch('playback-ended');
    }

    /**
     * Play previous song using doubly linked list traversal (backward)
     * Uses history as the "prev" pointer chain
     */
    #[On('request-previous-song')]
    public function playPreviousFromHistory()
    {
        if (count($this->history) > 0) {
            // Get previous song from history (traverse prev pointer)
            $prevSong = array_shift($this->history);
            
            // Add current song back to front of source songs (it becomes next)
            if ($this->currentSong) {
                array_unshift($this->sourceSongs, $this->currentSong);
            }
            
            // Update current song pointer
            $this->currentSongId = $prevSong['id'];
            $this->currentSong = $prevSong;
            
            $this->dispatch('play-previous-song', songId: $prevSong['id']);
            return;
        }
        
        // If no history but repeat mode is 'all', go to the last song (circular)
        if ($this->repeatMode === 'all' && count($this->originalSource) > 0) {
            // In circular list, prev of head is tail
            // Rebuild: current becomes next, last song becomes current
            if ($this->currentSong) {
                array_unshift($this->sourceSongs, $this->currentSong);
            }
            
            // Get the last song from original source
            $lastSong = end($this->originalSource);
            
            // Rebuild source: all songs except the last one
            $this->sourceSongs = array_slice($this->originalSource, 0, -1);
            
            $this->currentSongId = $lastSong['id'];
            $this->currentSong = $lastSong;
            
            $this->dispatch('play-previous-song', songId: $lastSong['id']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.components.queue');
    }
}
