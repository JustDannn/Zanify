<?php

namespace App\Livewire\Admin;

use App\Models\Song;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'songs' => Song::latest()->get()
        ]);
    }
}
