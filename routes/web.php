<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// --- AREA ADMIN (LIBRARY CRUD) ---
Route::prefix('admin')->group(function () {
    // 1. Read (Index)
    Route::get('/library', [AdminController::class, 'index']);
    
    // 2. Create (Add)
    Route::post('/library/add', [AdminController::class, 'add']);
    
    // 3. Delete
    Route::get('/library/delete/{id}', [AdminController::class, 'delete']);

    // 4. Sync Azure
    Route::get('/library/sync', [AdminController::class, 'syncFromAzure']);
    
    // 5. EDIT & UPDATE (INI YANG HILANG SEBELUMNYA)
    Route::get('/library/edit/{id}', [AdminController::class, 'edit']);
    Route::post('/library/update/{id}', [AdminController::class, 'update']);
});

// --- AREA USER (PLAYLIST & QUEUE) ---
Route::get('/playlist', [PlaylistController::class, 'index']);
Route::get('/playlist/add', [PlaylistController::class, 'addSong']);
Route::get('/playlist/queue', [PlaylistController::class, 'addToQueue']);
Route::get('/playlist/play', [PlaylistController::class, 'playSong']);

// --- FUNGSI MANIPULASI QUEUE (AJAX ENDPOINTS) ---
Route::get('/queue/clear', [PlaylistController::class, 'clearQueue']);
Route::get('/queue/move', [PlaylistController::class, 'moveQueueItem']);