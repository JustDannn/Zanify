<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Navbar extends Component
{
    public string $query = '';

    public function updatedQuery()
    {
        // Navigate to search page when user types
        if (strlen($this->query) >= 2) {
            $this->redirect(route('search', ['q' => $this->query]), navigate: true);
        }
    }

    public function goToSearch()
    {
        if (strlen($this->query) >= 1) {
            $this->redirect(route('search', ['q' => $this->query]), navigate: true);
        }
    }

    public function clearSearch()
    {
        $this->query = '';
    }

    public function render()
    {
        return view('livewire.components.navbar');
    }
}
