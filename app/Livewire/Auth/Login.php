<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    public function mount()
{
    if (auth()->check()) {
        return redirect('/');
    }
}

public function login()
{
    $this->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Login admin (tanpa database)
    if ($this->email === env('ADMIN_EMAIL') && $this->password === env('ADMIN_PASSWORD')) {

        session(['is_admin' => true]); // tandai admin login
        
        return redirect('/'); // <<< admin masuk ke home biasa
    }
// User login biasa
if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
    return $this->addError('email', 'Email atau password salah.');
}

// Pastikan user biasa BUKAN admin
session()->forget('is_admin');

return redirect()->intended('/');

}

public function render()
{
    return view('livewire.auth.login')
        ->layout('components.layouts.auth');
}

}
