<?php

namespace App\Livewire\Components;

use App\Models\Artist;
use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithPlaylistActions;

class ArtistDetail extends Component
{
    use WithPlaylistActions;

    public ?Artist $artist = null;
    public $popularSongs = [];
    public $albums = [];
    public int $totalListeners = 0;
    public int $totalPlays = 0;

    public function mount(int $id)
    {
        $this->artist = Artist::with(['songs.artists', 'albums'])->find($id);
        
        if (!$this->artist) {
            return redirect()->route('home');
        }

        $this->loadData();
    }

    public function loadData()
    {
        // Get popular songs (sorted by play count)
        $this->popularSongs = $this->artist->songs()
            ->with('artists')
            ->orderByDesc('play_count')
            ->take(10)
            ->get();

        // Get albums
        $this->albums = $this->artist->albums()
            ->withCount('songs')
            ->orderByDesc('year')
            ->get();

        // Calculate totals
        $this->totalPlays = $this->artist->songs()->sum('play_count');
        $this->totalListeners = $this->artist->songs()->sum('listeners');
    }

    /**
     * Play a song from this artist
     */
    public function playSong(int $songId)
    {
        $songIds = $this->popularSongs->pluck('id')->toArray();
        $this->dispatch('set-play-source', 
            sourceName: $this->artist->name, 
            songIds: $songIds, 
            startFromSongId: $songId
        );
        $this->dispatch('play-song', songId: $songId);
    }

    /**
     * Play all popular songs
     */
    public function playArtist()
    {
        if ($this->popularSongs->isEmpty()) return;
        
        $firstSong = $this->popularSongs->first();
        $this->playSong($firstSong->id);
    }

    /**
     * Add song to queue
     */
    public function addToQueue(int $songId)
    {
        $this->dispatch('add-to-queue', songId: $songId);
    }

    /**
     * Toggle like for a song
     */
    public function toggleLike(int $songId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $song = Song::find($songId);
        if ($song) {
            Auth::user()->toggleLike($song);
            $this->dispatch('songLikeToggled', songId: $songId, liked: Auth::user()->hasLiked($song));
        }
    }

    /**
     * Check if song is liked
     */
    public function isLiked(int $songId): bool
    {
        if (!Auth::check()) return false;
        $song = Song::find($songId);
        return $song ? Auth::user()->hasLiked($song) : false;
    }

    /**
     * Format listeners count
     */
    public function formatListeners(): string
    {
        if ($this->totalListeners >= 1000000) {
            return number_format($this->totalListeners / 1000000, 1) . 'M';
        } elseif ($this->totalListeners >= 1000) {
            return number_format($this->totalListeners / 1000, 1) . 'K';
        }
        return number_format($this->totalListeners);
    }

    public function render()
    {
        return view('livewire.components.artist-detail')->layout('components.layouts.app');
    }
}
