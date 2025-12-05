<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// --- AREA ADMIN (LIBRARY CRUD) ---
Route::prefix('admin')->group(function () {
    Route::get('/library', [AdminController::class, 'index']);
    Route::post('/library/add', [AdminController::class, 'add']);
    Route::get('/library/delete/{id}', [AdminController::class, 'delete']);
});

// --- AREA USER (PLAYLIST & QUEUE) ---
// Semua rute user dan fungsionalitas Queue diletakkan di satu blok Controller
Route::get('/playlist', [PlaylistController::class, 'index']);
Route::get('/playlist/add', [PlaylistController::class, 'addSong']);
Route::get('/playlist/queue', [PlaylistController::class, 'addToQueue']);
Route::get('/playlist/play', [PlaylistController::class, 'playSong']);

// --- FUNGSI MANIPULASI QUEUE (AJAX ENDPOINTS) ---
Route::get('/queue/clear', [PlaylistController::class, 'clearQueue']);
Route::get('/queue/move', [PlaylistController::class, 'moveQueueItem']);