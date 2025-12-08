<?php

namespace App\Livewire\Components;

use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;
use Livewire\Component;
use Livewire\Attributes\On;

class SearchResults extends Component
{
    public string $query = '';
    public bool $isSearching = false;
    public $songs = [];
    public $albums = [];
    public $topResult = null;

    #[On('search-updated')]
    public function updateSearch($query)
    {
        $this->query = $query;
        
        if (strlen($query) < 1) {
            $this->isSearching = false;
            $this->songs = [];
            $this->albums = [];
            $this->topResult = null;
            return;
        }

        $this->isSearching = true;
        $this->performSearch();
    }

    public function performSearch()
    {
        // Search songs
        $this->songs = Song::with(['album', 'artists'])
            ->where('title', 'like', '%' . $this->query . '%')
            ->orWhere('artist_name', 'like', '%' . $this->query . '%')
            ->orderBy('play_count', 'desc')
            ->take(6)
            ->get();

        // Search albums
        $this->albums = Album::with('artist')
            ->where('title', 'like', '%' . $this->query . '%')
            ->take(6)
            ->get();

        // Top result (first song or album match)
        if ($this->songs->isNotEmpty()) {
            $this->topResult = [
                'type' => 'song',
                'item' => $this->songs->first()
            ];
        } elseif ($this->albums->isNotEmpty()) {
            $this->topResult = [
                'type' => 'album',
                'item' => $this->albums->first()
            ];
        } else {
            $this->topResult = null;
        }
    }

    public function playSong(int $songId)
    {
        // Dispatch to Player component
        $this->dispatch('play-song', songId: $songId);
    }

    /**
     * Add song to queue
     */
    public function addToQueue(int $songId)
    {
        $this->dispatch('add-to-queue', songId: $songId);
    }

    /**
     * Toggle like status for a song
     */
    public function toggleLike(int $songId)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('login');
        }

        $song = Song::find($songId);
        if ($song) {
            $liked = \Illuminate\Support\Facades\Auth::user()->toggleLike($song);
            
            // Dispatch event to update other components (player, liked-songs page, etc.)
            $this->dispatch('songLikeToggled', songId: $songId, liked: $liked);
        }
    }

    /**
     * Check if current user has liked a song
     */
    public function isLiked(int $songId): bool
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return false;
        }
        
        $song = Song::find($songId);
        return $song ? \Illuminate\Support\Facades\Auth::user()->hasLiked($song) : false;
    }

    public function render()
    {
        $likedSongIds = [];
        if (\Illuminate\Support\Facades\Auth::check()) {
            $likedSongIds = \Illuminate\Support\Facades\Auth::user()
                ->likedSongs()
                ->pluck('songs.id')
                ->toArray();
        }
        
        return view('livewire.components.search-results', [
            'likedSongIds' => $likedSongIds
        ]);
    }
}
