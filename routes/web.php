<?php
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Components\PlaylistPage;

// Guest Routes (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Protected Routes (Authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => view('index'))->name('home');

    Route::get('/playlist/{id}', PlaylistPage::class)->name('playlist');
    
    // Logout
    Route::post('/logout', function () {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
