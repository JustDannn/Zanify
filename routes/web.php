<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTES BACKEND ---
Route::get('/playlist', [PlaylistController::class, 'index']);
Route::get('/playlist/add', [PlaylistController::class, 'addSong']);

