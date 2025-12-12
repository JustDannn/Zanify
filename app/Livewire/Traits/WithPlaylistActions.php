<?php

namespace App\Livewire\Traits;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Support\Facades\Auth;

trait WithPlaylistActions
{
    public ?int $showPlaylistMenuForSong = null;
    public array $userPlaylists = [];

    public function loadUserPlaylists()
    {
        if (Auth::check()) {
            $this->userPlaylists = Auth::user()->playlists()->get()->toArray();
        }
    }

    public function togglePlaylistMenu(int $songId)
    {
        if ($this->showPlaylistMenuForSong === $songId) {
            $this->showPlaylistMenuForSong = null;
        } else {
            $this->loadUserPlaylists();
            $this->showPlaylistMenuForSong = $songId;
        }
    }

    public function closePlaylistMenu()
    {
        $this->showPlaylistMenuForSong = null;
    }

    public function addToPlaylist(int $playlistId, int $songId)
    {
        if (!Auth::check()) return;

        $playlist = Playlist::where('id', $playlistId)
            ->where('user_id', Auth::id())
            ->first();

        $song = Song::find($songId);

        if ($playlist && $song) {
            // Check if song already in playlist
            if ($playlist->songs()->where('song_id', $songId)->exists()) {
                $this->dispatch('notify', message: "'{$song->title}' is already in '{$playlist->name}'");
            } else {
                $maxOrder = $playlist->songs()->max('order') ?? 0;
                $playlist->songs()->attach($songId, ['order' => $maxOrder + 1]);
                $this->dispatch('notify', message: "Added '{$song->title}' to '{$playlist->name}'");
            }
        }

        $this->showPlaylistMenuForSong = null;
        
        // Dispatch event to update library
        $this->dispatch('playlistUpdated');
    }

    public function removeFromPlaylist(int $playlistId, int $songId)
    {
        if (!Auth::check()) return;

        $playlist = Playlist::where('id', $playlistId)
            ->where('user_id', Auth::id())
            ->first();

        $song = Song::find($songId);

        if ($playlist && $song) {
            $playlist->songs()->detach($songId);
            $this->dispatch('notify', message: "Removed '{$song->title}' from '{$playlist->name}'");
        }

        $this->dispatch('playlistUpdated');
    }

    public function createPlaylistWithSong(int $songId)
    {
        if (!Auth::check()) return;

        $song = Song::find($songId);
        $playlistCount = Auth::user()->playlists()->count();
        $playlistName = 'My Playlist #' . ($playlistCount + 1);
        
        $playlist = Playlist::create([
            'user_id' => Auth::id(),
            'name' => $playlistName,
        ]);

        $playlist->songs()->attach($songId, ['order' => 1]);

        $this->showPlaylistMenuForSong = null;
        $this->dispatch('playlistUpdated');
        
        if ($song) {
            $this->dispatch('notify', message: "Created '{$playlistName}' with '{$song->title}'");
        }
    }

    public function isInPlaylist(int $playlistId, int $songId): bool
    {
        $playlist = Playlist::find($playlistId);
        return $playlist ? $playlist->songs()->where('song_id', $songId)->exists() : false;
    }
}
