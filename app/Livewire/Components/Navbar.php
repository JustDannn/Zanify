<?php
namespace App\Livewire;

use Livewire\Component;

class Navbar extends Component
{
    public string $query = '';     // ⬅️ INI HARUS ADA
    public ?array $suggestions = [];

    public function mount()
    {
        $this->suggestions = [];
    }

    public function updatedQuery()
    {
        if (strlen($this->query) < 1) {
            $this->suggestions = [];
            return;
        }

        $data = [
            'Valorant',
            'GTA V',
            'The Witcher 3',
            'Elden Ring',
            'Baldur’s Gate 3',
            'Roblox',
            'Fortnite',
            'Minecraft',
            'League of Legends',
        ];

        $this->suggestions = collect($data)
            ->filter(fn ($item) => str_contains(strtolower($item), strtolower($this->query)))
            ->take(5)
            ->values()
            ->toArray();
    }

    public function selectSuggestion($text)
    {
        $this->query = $text;
        $this->suggestions = [];
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
