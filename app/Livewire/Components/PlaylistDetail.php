<?php

namespace App\Livewire\Components;

use App\Models\Playlist;
use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\WithPlaylistActions;

class PlaylistDetail extends Component
{
    use WithPlaylistActions;

    public ?Playlist $playlist = null;
    public $songs = [];
    public bool $isEditingName = false;
    public string $editingName = '';

    public function mount(int $id)
    {
        $this->playlist = Playlist::with(['songs.artists', 'user'])
            ->where('id', $id)
            ->firstOrFail();
            
        $this->loadSongs();
    }

    public function loadSongs()
    {
        if ($this->playlist) {
            $this->songs = $this->playlist->songs()->with('artists')->get();
        }
    }

    public function startEditingName()
    {
        if (Auth::id() !== $this->playlist->user_id) return;
        
        $this->isEditingName = true;
        $this->editingName = $this->playlist->name;
    }

    public function updateName()
    {
        if (Auth::id() !== $this->playlist->user_id) return;
        
        $name = trim($this->editingName);
        
        if (!empty($name)) {
            $this->playlist->update(['name' => $name]);
            $this->playlist->refresh();
        }
        
        $this->isEditingName = false;
        $this->editingName = '';
        
        // Notify library to refresh
        $this->dispatch('playlistUpdated');
    }

    public function cancelEditingName()
    {
        $this->isEditingName = false;
        $this->editingName = '';
    }

    /**
     * Remove song from playlist
     */
    public function removeSong(int $songId)
    {
        if (Auth::id() !== $this->playlist->user_id) return;
        
        $this->playlist->songs()->detach($songId);
        $this->loadSongs();
        
        // Notify library to refresh
        $this->dispatch('playlistUpdated');
    }

    /**
     * Play a song and set up autoplay from this playlist
     */
    public function playSong(int $songId)
    {
        $song = $this->songs->firstWhere('id', $songId);
        if (!$song) return;
        
        // Set the source for autoplay
        $songIds = $this->songs->pluck('id')->toArray();
        $this->dispatch('set-play-source', sourceName: $this->playlist->name, songIds: $songIds, startFromSongId: $songId);
        
        // Send full song data to avoid DB query in Player
        $isLiked = Auth::check() ? Auth::user()->hasLiked($song) : false;
        $this->dispatch('play-song-data', songData: [
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
            
            // Dispatch event to update other components
            $this->dispatch('songLikeToggled', songId: $songId, liked: Auth::user()->hasLiked($song));
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
     * Check if song is liked
     */
    public function isLiked(int $songId): bool
    {
        if (!Auth::check()) return false;
        
        return Auth::user()->likedSongs()->where('song_id', $songId)->exists();
    }

    public function render()
    {
        return view('livewire.components.playlist-detail');
    }
}
