<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Models\Playlist;

class Library extends Component
{
    public array $items = [];
    public bool $isCreatingPlaylist = false;
    public string $newPlaylistName = '';
    public ?int $editingPlaylistId = null;
    public string $editingPlaylistName = '';

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

    #[On('playlistUpdated')]
    public function onPlaylistUpdated()
    {
        $this->loadLibrary();
    }

    public function startCreatingPlaylist()
    {
        if (!Auth::check()) return;
        
        $this->isCreatingPlaylist = true;
        $this->newPlaylistName = '';
    }

    public function cancelCreatingPlaylist()
    {
        $this->isCreatingPlaylist = false;
        $this->newPlaylistName = '';
    }

    public function createPlaylist()
    {
        if (!Auth::check()) return;
        
        $name = trim($this->newPlaylistName);
        
        if (empty($name)) {
            $name = 'My Playlist #' . (Auth::user()->playlists()->count() + 1);
        }
        
        Playlist::create([
            'user_id' => Auth::id(),
            'name' => $name,
        ]);
        
        $this->isCreatingPlaylist = false;
        $this->newPlaylistName = '';
        $this->loadLibrary();
    }

    public function startEditingPlaylist(int $playlistId)
    {
        $playlist = Playlist::where('id', $playlistId)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($playlist) {
            $this->editingPlaylistId = $playlistId;
            $this->editingPlaylistName = $playlist->name;
        }
    }

    public function updatePlaylistName()
    {
        if (!$this->editingPlaylistId) return;
        
        $name = trim($this->editingPlaylistName);
        
        if (!empty($name)) {
            Playlist::where('id', $this->editingPlaylistId)
                ->where('user_id', Auth::id())
                ->update(['name' => $name]);
        }
        
        $this->editingPlaylistId = null;
        $this->editingPlaylistName = '';
        $this->loadLibrary();
    }

    public function cancelEditingPlaylist()
    {
        $this->editingPlaylistId = null;
        $this->editingPlaylistName = '';
    }

    public function deletePlaylist(int $playlistId)
    {
        Playlist::where('id', $playlistId)
            ->where('user_id', Auth::id())
            ->delete();
            
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
            
            // User's playlists
            $playlists = Auth::user()->playlists()->withCount('songs')->get();
            
            foreach ($playlists as $playlist) {
                $this->items[] = [
                    'type' => 'playlist',
                    'id' => $playlist->id,
                    'title' => $playlist->name,
                    'subtitle' => 'Playlist · ' . $playlist->songs_count . ' ' . \Illuminate\Support\Str::plural('song', $playlist->songs_count),
                    'image' => $playlist->cover_image,
                    'route' => route('playlist.show', $playlist->id),
                    'is_liked_songs' => false,
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.components.library');
    }
}

