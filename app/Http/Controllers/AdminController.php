<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataStructures\DoublyLinkedList; // DLL untuk Library Utama
// Gunakan class Node yang sudah di-upgrade ke Doubly
use App\DataStructures\Node;

class AdminController extends Controller
{
    /**
     * Helper: Muat DLL Library dari Session
     */
    private function loadLibrary(Request $request)
    {
        if ($request->session()->has('library')) {
            // Unserialize data dari session
            return unserialize($request->session()->get('library'));
        }
        // Jika kosong, buat Library baru
        return new DoublyLinkedList();
    }

    /**
     * Menampilkan Halaman CRUD Admin
     * URL: /admin/library
     */
    public function index(Request $request)
    {
        $library = $this->loadLibrary($request);
        $songs = $library->getAllSongs();

        return view('admin.library', [
            'songs' => $songs,
            'total' => $library->count
        ]);
    }

    /**
     * Menambah Lagu Baru ke DLL Library (CRUD: Create)
     * URL: /admin/library/add
     */
    public function add(Request $request)
    {
        $library = $this->loadLibrary($request);
        
        // Simulasi Input Form: Cek apakah ada data yang dikirimkan
        if ($request->isMethod('post')) {

            // =========================================================
            // TEMPAT INTEGRASI AZURE/CLOUD STORAGE KEY LOGIC
            // =========================================================
            // Jika Anda ingin menggunakan base URL dari Azure Container,
            // Anda bisa definisikan di sini. Misalnya:
            // const AZURE_BASE_URL = 'https://[nama-storage].blob.core.windows.net/[nama-container]/';
            // $fullUrl = AZURE_BASE_URL . $request->input('audio_url');
            // ---------------------------------------------------------

            // Untuk saat ini, kita menggunakan URL lengkap yang diinput Admin.
            $finalAudioUrl = $request->input('audio_url'); 
            
            $songData = [
                'id' => uniqid(),
                'title' => $request->input('title'),
                'artist' => $request->input('artist'),
                'album' => $request->input('album'),
                'genre' => $request->input('genre'),
                'duration' => $request->input('duration'),
                // Gunakan URL yang diinput Admin
                'audio_url' => $finalAudioUrl 
            ];
            
            // Masukkan ke DLL Library
            $library->addSong($songData);
            
            // Simpan kembali ke session
            $request->session()->put('library', serialize($library));
            
            // Redirect kembali ke halaman admin
            return redirect('/admin/library')->with('success', 'Lagu baru berhasil ditambahkan.');
        }

        // Jika method GET (hanya menampilkan form tambah), kita redirect ke index
        return redirect('/admin/library');
    }

    /**
     * Menghapus Lagu dari DLL Library (CRUD: Delete)
     * URL: /admin/library/delete/{id}
     */
    public function delete(Request $request, $id)
    {
        $library = $this->loadLibrary($request);

        // Hapus lagu dari DLL
        $isDeleted = $library->deleteSongById($id);

        // Simpan kembali ke session
        $request->session()->put('library', serialize($library));

        // Redirect kembali
        if ($isDeleted) {
            return redirect('/admin/library')->with('success', 'Lagu berhasil dihapus.');
        } else {
            return redirect('/admin/library')->with('error', 'ID Lagu tidak ditemukan.');
        }
    }
}