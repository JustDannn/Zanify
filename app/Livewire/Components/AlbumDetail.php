<?php

namespace App\Livewire\Components;

use App\Models\Album;
use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithPlaylistActions;

class AlbumDetail extends Component
{
    use WithPlaylistActions;

    public ?Album $album = null;
    public $songs = [];
    public int $totalDuration = 0;
    public int $totalPlays = 0;

    public function mount(int $id)
    {
        $this->album = Album::with(['artist', 'songs.artists'])->find($id);
        
        if (!$this->album) {
            return redirect()->route('home');
        }

        $this->loadSongs();
    }

    public function loadSongs()
    {
        $this->songs = $this->album->songs()
            ->with('artists')
            ->orderBy('created_at')
            ->get();

        $this->totalDuration = $this->songs->sum('duration');
        $this->totalPlays = $this->songs->sum('play_count');
    }

    /**
     * Play a song from this album
     */
    public function playSong(int $songId)
    {
        $song = $this->songs->firstWhere('id', $songId);
        if (!$song) return;
        
        $songIds = $this->songs->pluck('id')->toArray();
        $this->dispatch('set-play-source', 
            sourceName: $this->album->title, 
            songIds: $songIds, 
            startFromSongId: $songId
        );
        
        // Send full song data to avoid DB query in Player
        $isLiked = Auth::check() ? Auth::user()->hasLiked($song) : false;
        $this->dispatch('play-song-data', songData: [
            'id' => $song->id,
            'title' => $song->title,
            'artist' => $song->artist_display,
            'cover' => $song->cover_url ?? $this->album->cover_url,
            'audioUrl' => $song->audio_url,
            'duration' => $song->duration,
            'isLiked' => $isLiked,
        ]);
    }

    /**
     * Play entire album from start
     */
    public function playAlbum()
    {
        if ($this->songs->isEmpty()) return;
        
        $firstSong = $this->songs->first();
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
     * Format duration
     */
    public function formatTotalDuration(): string
    {
        $hours = floor($this->totalDuration / 3600);
        $minutes = floor(($this->totalDuration % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours} hr {$minutes} min";
        }
        return "{$minutes} min";
    }

    public function render()
    {
        return view('livewire.components.album-detail')->layout('components.layouts.app');
    }
}
