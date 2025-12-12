<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Components\LikedSongs;
use App\Livewire\Components\RecentlyPlayed;
use App\Livewire\Components\AlbumDetail;
use App\Livewire\Components\ArtistDetail;
use App\Livewire\Components\PlaylistDetail;
use App\Livewire\Components\SearchResults;

Route::get('/', function () {
    if (!Auth::check() && !session('is_admin')) {
        return redirect()->route('login');
    }
    return view('index');
})->name('home');
Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/search', SearchResults::class)->name('search');
Route::get('/liked-songs', LikedSongs::class)->name('liked-songs');
Route::get('/recently-played', RecentlyPlayed::class)->name('recently-played');
Route::get('/album/{id}', AlbumDetail::class)->name('album');
Route::get('/artist/{id}', ArtistDetail::class)->name('artist');
Route::get('/playlist/{id}', PlaylistDetail::class)->name('playlist.show');
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->name('admin.admin-dashboard');
});
