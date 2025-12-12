<?php

namespace App\Livewire\Components;

use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithPlaylistActions;

class LikedSongs extends Component
{
    use WithPlaylistActions;

    public $songs = [];

    public function mount()
    {
        // Only authenticated users (not admin via session) can access liked songs
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->loadLikedSongs();
    }

    #[On('songLikeToggled')]
    public function onSongLikeToggled(?int $songId = null, ?bool $liked = null)
    {
        // Refresh liked songs list when a song is liked/unliked
        $this->loadLikedSongs();
    }

    public function loadLikedSongs()
    {
        if (Auth::check()) {
            $this->songs = Auth::user()->likedSongs()->with('artists')->get();
        } else {
            $this->songs = collect([]);
        }
    }

    /**
     * Toggle like status for a song
     */
    public function toggleLike(int $songId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $song = Song::find($songId);
        if ($song) {
            Auth::user()->toggleLike($song);
            $this->loadLikedSongs();
            
            // Dispatch event to update other components
            $this->dispatch('songLikeToggled', songId: $songId, liked: Auth::user()->hasLiked($song));
        }
    }

    /**
     * Play a song and set up autoplay from Liked Songs
     */
    public function playSong(int $songId)
    {
        // Set the source for autoplay
        $songIds = $this->songs->pluck('id')->toArray();
        $this->dispatch('set-play-source', sourceName: 'Liked Songs', songIds: $songIds, startFromSongId: $songId);
        
        // Play the song
        $this->dispatch('play-song', songId: $songId);
    }

    /**
     * Add song to queue
     */
    public function addToQueue(int $songId)
    {
        $this->dispatch('add-to-queue', songId: $songId);
    }

    public function render()
    {
        return view('livewire.components.liked-songs');
    }
}
