<?php

namespace App\Livewire\Components;

use Livewire\Component;

class PlaylistPage extends Component
{
    public $playlist = [];

    public function mount()
    {
        $this->playlist = [
            'title' => 'Daily Mix 1',
            'description' => 'A mix of songs you love',
            'subtitle' => 'Updated every day',
            'made_for' => 'You',
            'total_songs' => 50,
            'duration' => '2h 35m'
        ];
    }

    public function render()
    {
        return view('livewire.components.playlist-page');
    }
}