<?php

namespace App\Livewire\Components;

use App\Models\Song;
use App\Models\Album;
use Livewire\Component;

class Navbar extends Component
{
    public string $query = '';
    public $suggestions = [];

    public function mount()
    {
        $this->suggestions = [];
    }

    public function updatedQuery()
    {
        // Dispatch event to SearchResults component
        $this->dispatch('search-updated', query: $this->query);

        if (strlen($this->query) < 2) {
            $this->suggestions = [];
            return;
        }

        // Get quick suggestions for dropdown
        $songs = Song::where('title', 'like', '%' . $this->query . '%')
            ->orWhere('artist_name', 'like', '%' . $this->query . '%')
            ->take(5)
            ->get()
            ->map(fn($song) => [
                'type' => 'song',
                'id' => $song->id,
                'title' => $song->title,
                'subtitle' => $song->artist_display,
                'cover' => $song->cover_url,
            ]);

        $albums = Album::where('title', 'like', '%' . $this->query . '%')
            ->take(3)
            ->get()
            ->map(fn($album) => [
                'type' => 'album',
                'id' => $album->id,
                'title' => $album->title,
                'subtitle' => $album->artist_name,
                'cover' => $album->cover_url,
            ]);

        $this->suggestions = $songs->concat($albums)->take(6)->toArray();
    }

    public function selectSuggestion($type, $id)
    {
        // Handle selection if needed
        $this->suggestions = [];
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->suggestions = [];
        $this->dispatch('search-updated', query: '');
    }

    public function render()
    {
        return view('livewire.components.navbar');
    }
}
