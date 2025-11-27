<?php

namespace App\Livewire\Cards;

use Livewire\Component;

class MiniCard extends Component
{
    // Ini adalah properti yang menerima data dari CardGroup
    public $cardData=[]; 

    public function render()
    {
        return view('livewire.cards.mini-card');
    }
}