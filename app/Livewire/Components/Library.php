<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class Library extends Component
{
    public array $items = [];

    public function mount()
    {
        $this->loadLibrary();
    }

    #[On('songLikeToggled')]
    public function onSongLikeToggled(?int $songId = null, ?bool $liked = null)
    {
        // Just refresh the library when a song is liked/unliked
        $this->loadLibrary();
    }

    public function loadLibrary()
    {
        $this->items = [];
        
        // Liked Songs - only for authenticated users (not admin)
        // Admin uses session-based auth without a database record, so can't have liked songs
        if (Auth::check()) {
            $likedCount = Auth::user()->likedSongs()->count();
            
            $this->items[] = [
                'type' => 'playlist',
                'title' => 'Liked Songs',
                'subtitle' => 'Playlist · ' . $likedCount . ' ' . \Illuminate\Support\Str::plural('song', $likedCount),
                'image' => null, // Special gradient
                'route' => route('liked-songs'),
                'is_liked_songs' => true,
            ];
        }
        
        // You can add more library items here later (playlists, followed artists, etc.)
    }

    public function render()
    {
        return view('livewire.components.library');
    }
}

