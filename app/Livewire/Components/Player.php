<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Song;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

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

    #[On('play-song')]
    public function playSong(int $songId)
    {
        $this->loadAndPlaySong($songId);
    }

    #[On('play-previous-song')]
    public function playPreviousSong(int $songId)
    {
        $this->loadAndPlaySong($songId, false); // Don't record to history
    }

    private function loadAndPlaySong(int $songId, bool $recordHistory = true)
    {
        $song = Song::find($songId);
        
        if (!$song) {
            return;
        }

        $this->currentSongId = $song->id;
        $this->title = $song->title;
        $this->artist = $song->artist_display;
        $this->cover = $song->cover_url;
        $this->audioUrl = $song->audio_url;
        $this->duration = $song->duration;
        $this->isVisible = true;
        
        // Check if song is liked
        $this->isLiked = Auth::check() ? Auth::user()->hasLiked($song) : false;

        // Record play history for authenticated users
        if ($recordHistory && Auth::check()) {
            Auth::user()->recordPlay($song);
            $this->dispatch('recently-played-updated');
        }

        // Dispatch event to Alpine.js to actually play the audio
        $this->dispatch('song-loaded', [
            'id' => $song->id,
            'title' => $song->title,
            'artist' => $song->artist_display,
            'cover' => $song->cover_url,
            'audioUrl' => $song->audio_url,
            'duration' => $song->duration,
            'isLiked' => $this->isLiked,
        ]);
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
