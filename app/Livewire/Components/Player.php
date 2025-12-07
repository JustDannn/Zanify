<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Song;
use Livewire\Attributes\On;

class Player extends Component
{
    public ?int $currentSongId = null;
    public ?string $title = null;
    public ?string $artist = null;
    public ?string $cover = null;
    public ?string $audioUrl = null;
    public ?int $duration = null;
    public bool $isVisible = false;

    #[On('play-song')]
    public function playSong(int $songId)
    {
        $song = Song::find($songId);
        
        if (!$song) {
            return;
        }

        $this->currentSongId = $song->id;
        $this->title = $song->title;
        $this->artist = $song->artist_display;
        $this->cover = $song->cover_url;
        $this->audioUrl = $song->audio_url;
        $this->duration = $song->duration;
        $this->isVisible = true;

        // Dispatch event to Alpine.js to actually play the audio
        $this->dispatch('song-loaded', [
            'id' => $song->id,
            'title' => $song->title,
            'artist' => $song->artist_display,
            'cover' => $song->cover_url,
            'audioUrl' => $song->audio_url,
            'duration' => $song->duration,
        ]);
    }

    public function render()
    {
        return view('livewire.components.player');
    }
}
