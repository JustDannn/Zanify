<?php

namespace App\Livewire;

use Livewire\Component;

class CardGroup extends Component
{
    public $cards = [
        [
            'title' => 'Menari Dengan Bayangan',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=b6e3f4',
            'type' => 'Album',
            'has_play_button' => true,
        ],
        [
            'title' => 'kalcer indo',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=c0aede',
            'type' => 'Playlist',
            'has_play_button' => true,
        ],
        [
            'title' => 'Instrumental Studying',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=d1d4f9',
            'type' => 'Playlist',
            'has_play_button' => true,
        ],
        [
            'title' => '2012 vibes',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=6247eb',
            'type' => 'Playlist',
            'has_play_button' => true,
        ],
        [
            'title' => 'JJ FYP TIKTOK 2025',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=ebeb47',
            'type' => 'Playlist',
            'has_play_button' => true,
        ],
        [
            'title' => 'dancing in the night',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=47b4eb',
            'type' => 'Playlist',
            'has_play_button' => true,
        ],
        [
            'title' => 'Sore: Istri dari Masa Depan',
            'image' => 'https://api.dicebear.com/9.x/glass/png?backgroundColor=4762eb',
            'type' => 'Album',
            'has_play_button' => true,
        ],
        [
            'title' => 'Liked Songs',
            'image' => null, // Untuk kartu khusus 'Liked Songs'
            'type' => 'Playlist',
            'has_play_button' => true,
        ],
    ];

    public function render()
    {
        return view('livewire.card-group');
    }
}