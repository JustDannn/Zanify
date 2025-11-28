<?php

namespace App\Livewire\Components;

use Livewire\Component;

class MixSection extends Component
{
    // Data dummy untuk kartu Daily Mix
    public $mixes = [
        [
            'id' => 1,
            'title' => 'Reality Club, Hindia, Perunggu and more',
            'image' => 'https://picsum.photos/id/1018/300/300',
            'mix_number' => '01',
        ],
        [
            'id' => 2,
            'title' => 'Taylor Swift, Bruno Mars, Maroon 5 and...',
            'image' => 'https://picsum.photos/id/1025/300/300',
            'mix_number' => '02',
        ],
        [
            'id' => 3,
            'title' => 'Yorushika, Sawano Hiroyuki, Eve and...',
            'image' => 'https://picsum.photos/id/1069/300/300',
            'mix_number' => '03',
        ],
        [
            'id' => 4,
            'title' => '.Feast, Marcello Tahitoe, Nike Ardilla...',
            'image' => 'https://picsum.photos/id/1024/300/300',
            'mix_number' => '04',
        ],
        [
            'id' => 5,
            'title' => 'Conan Gray, Clairo, Men I Trust and more',
            'image' => 'https://picsum.photos/id/1033/300/300',
            'mix_number' => '05',
        ],
        // Tambahkan mix lain jika perlu
    ];

    public function render()
    {
        return view('livewire.components.mix-section');
    }
}