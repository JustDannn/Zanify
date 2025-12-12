<?php

namespace App\Livewire\Components;

use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithPlaylistActions;

class SearchResults extends Component
{
    use WithPlaylistActions;

    #[Url(as: 'q')]
    public string $query = '';
    
    public bool $isSearching = false;
    public $songs = [];
    public $albums = [];
    public $artists = [];
    public $topResult = null;

    public function mount()
    {
        // Perform search if query exists in URL
        if (!empty($this->query)) {
            $this->isSearching = true;
            $this->performSearch();
        }
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 1) {
            $this->isSearching = false;
            $this->songs = [];
            $this->albums = [];
            $this->artists = [];
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

        // Search artists
        $this->artists = Artist::where('name', 'like', '%' . $this->query . '%')
            ->take(6)
            ->get();

        // Top result - prioritize exact matches
        $exactSong = $this->songs->first(fn($s) => strtolower($s->title) === strtolower($this->query));
        $exactAlbum = $this->albums->first(fn($a) => strtolower($a->title) === strtolower($this->query));
        $exactArtist = $this->artists->first(fn($a) => strtolower($a->name) === strtolower($this->query));

        if ($exactArtist) {
            $this->topResult = ['type' => 'artist', 'item' => $exactArtist];
        } elseif ($exactAlbum) {
            $this->topResult = ['type' => 'album', 'item' => $exactAlbum];
        } elseif ($exactSong) {
            $this->topResult = ['type' => 'song', 'item' => $exactSong];
        } elseif ($this->artists->isNotEmpty()) {
            $this->topResult = ['type' => 'artist', 'item' => $this->artists->first()];
        } elseif ($this->songs->isNotEmpty()) {
            $this->topResult = ['type' => 'song', 'item' => $this->songs->first()];
        } elseif ($this->albums->isNotEmpty()) {
            $this->topResult = ['type' => 'album', 'item' => $this->albums->first()];
        } else {
            $this->topResult = null;
        }
    }

    public function playSong(int $songId)
    {
        // Set the source for autoplay from search results
        $songIds = $this->songs->pluck('id')->toArray();
        $this->dispatch('set-play-source', sourceName: 'Search Results', songIds: $songIds, startFromSongId: $songId);
        
        // Dispatch to Player component
        $this->dispatch('play-song', songId: $songId);
    }

    /**
     * Play all songs from an album
     */
    public function playAlbum(int $albumId)
    {
        $album = Album::with('songs')->find($albumId);
        if ($album && $album->songs->isNotEmpty()) {
            $songIds = $album->songs->pluck('id')->toArray();
            $this->dispatch('set-play-source', sourceName: $album->title, songIds: $songIds, startFromSongId: $songIds[0]);
            $this->dispatch('play-song', songId: $songIds[0]);
        }
    }

    /**
     * Play all songs from an artist
     */
    public function playArtist(int $artistId)
    {
        $artist = Artist::find($artistId);
        if ($artist) {
            $songs = Song::whereHas('artists', fn($q) => $q->where('artists.id', $artistId))
                ->orWhere('artist_name', $artist->name)
                ->orderBy('play_count', 'desc')
                ->get();
            
            if ($songs->isNotEmpty()) {
                $songIds = $songs->pluck('id')->toArray();
                $this->dispatch('set-play-source', sourceName: $artist->name, songIds: $songIds, startFromSongId: $songIds[0]);
                $this->dispatch('play-song', songId: $songIds[0]);
            }
        }
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
