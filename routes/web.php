<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Components\LikedSongs;
use App\Livewire\Components\RecentlyPlayed;

Route::get('/', function () {
    if (!Auth::check() && !session('is_admin')) {
        return redirect()->route('login');
    }
    return view('index');
})->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/liked-songs', LikedSongs::class)->name('liked-songs');
Route::get('/recently-played', RecentlyPlayed::class)->name('recently-played');
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->name('admin.admin-dashboard');
});
