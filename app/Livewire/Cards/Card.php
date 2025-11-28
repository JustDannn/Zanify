<?php

namespace App\Livewire\Cards;

use Livewire\Component;

class Card extends Component
{
    public $cardData;

    public function render()
    {
        if (is_null($this->cardData)) {
            $this->cardData = [];
        }
        return view('livewire.cards.card');
    }
}
