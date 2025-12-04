<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email = '';
    public $password = '';

        public function login()
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (! Auth::attempt($validated)) {
            $this->addError('email', 'Email atau password salah.');
            return;
        }

        session()->regenerate();

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }

    // single login method (merged and deduplicated)


}
