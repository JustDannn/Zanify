<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Song;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Player extends Component
{
    public ?int $currentSongId = null;
    public ?string $title = null;
    public ?string $artist = null;
    public ?string $cover = null;
    public ?string $audioUrl = null;
    public ?int $duration = null;
    public bool $isVisible = false;
    public bool $isLiked = false;

    /**
     * Play song with pre-loaded data (FAST - no DB query)
     */
    #[On('play-song-data')]
    public function playSongWithData(array $songData)
    {
        $this->currentSongId = $songData['id'];
        $this->title = $songData['title'];
        $this->artist = $songData['artist'];
        $this->cover = $songData['cover'];
        $this->audioUrl = $songData['audioUrl'];
        $this->duration = $songData['duration'] ?? null;
        $this->isVisible = true;
        $this->isLiked = $songData['isLiked'] ?? false;

        // Record play history async (don't block playback)
        if (Auth::check()) {
            $this->recordPlayAsync($this->currentSongId);
        }

        // Dispatch to Alpine.js
        $this->dispatch('song-loaded', [
            'id' => $this->currentSongId,
            'title' => $this->title,
            'artist' => $this->artist,
            'cover' => $this->cover,
            'audioUrl' => $this->audioUrl,
            'duration' => $this->duration,
            'isLiked' => $this->isLiked,
        ]);
    }

    /**
     * Fallback: Play song by ID (slower - requires DB query)
     */
    #[On('play-song')]
    public function playSong(int $songId)
    {
        // Try to get from cache first
        $song = Cache::remember("song_{$songId}", 3600, function () use ($songId) {
            return Song::with(['album', 'artists'])->find($songId);
        });
        
        if (!$song) {
            return;
        }

        $isLiked = Auth::check() ? Auth::user()->hasLiked($song) : false;

        $this->playSongWithData([
            'id' => $song->id,
            'title' => $song->title,
            'artist' => $song->artist_display,
            'cover' => $song->cover_url,
            'audioUrl' => $song->audio_url,
            'duration' => $song->duration,
            'isLiked' => $isLiked,
        ]);
    }

    /**
     * Record play history without blocking
     */
    private function recordPlayAsync(int $songId)
    {
        if (Auth::check()) {
            $song = Song::find($songId);
            if ($song) {
                Auth::user()->recordPlay($song);
                $this->dispatch('recently-played-updated');
            }
        }
    }

    /**
     * Toggle like for current playing song
     */
    public function toggleLike()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->currentSongId) {
            return;
        }

        $song = Song::find($this->currentSongId);
        if ($song) {
            $this->isLiked = Auth::user()->toggleLike($song);
            
            // Dispatch event to update other components
            $this->dispatch('songLikeToggled', songId: $this->currentSongId, liked: $this->isLiked);
        }
    }

    /**
     * Listen for like toggled events from other components
     */
    #[On('songLikeToggled')]
    public function onSongLikeToggled(int $songId, ?bool $liked = null)
    {
        if ($this->currentSongId === $songId) {
            // If liked status not provided, check from database
            if ($liked === null && Auth::check()) {
                $song = Song::find($songId);
                $liked = $song ? Auth::user()->hasLiked($song) : false;
            }
            $this->isLiked = $liked ?? false;
        }
    }

    public function render()
    {
        return view('livewire.components.player');
    }
}
