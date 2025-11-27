<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Library extends Component
{
    public array $items = [];

    public function mount()
    {
        // Dummy data dulu
        $this->items = [
            [
                'type' => 'playlist',
                'title' => 'Liked Songs',
                'subtitle' => 'Playlist · 1,175 songs',
                'image' => '/images/liked.png', // nanti ganti
            ],
            [
                'type' => 'playlist',
                'title' => 'dancing in the night',
                'subtitle' => 'Playlist · Dani Nurfatah',
                'image' => '/images/default.jpg',
            ],
            [
                'type' => 'artist',
                'title' => 'Mac DeMarco',
                'subtitle' => 'Artist',
                'image' => '/images/default.jpg',
            ],
            [
                'type' => 'playlist',
                'title' => 'NANGIS AMPE MATA BE...',
                'subtitle' => 'Playlist · @radionangis',
                'image' => '/images/default.jpg',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.components.library');
    }
}

