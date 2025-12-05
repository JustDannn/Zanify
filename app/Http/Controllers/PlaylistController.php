<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataStructures\SinglyLinkedList; // Memanggil file Linked List buatanmu

class PlaylistController extends Controller
{
    /**
     * Menampilkan isi Playlist (Linked List)
     * URL: /playlist
     */
    public function index(Request $request)
    {
        // 1. Cek apakah di memori (session) sudah ada Linked List?
        if ($request->session()->has('my_playlist')) {
            // Jika ada, ambil dan "hidupkan" kembali object-nya
            $playlist = unserialize($request->session()->get('my_playlist'));
        } else {
            // Jika belum ada, buat Linked List baru yang kosong
            $playlist = new SinglyLinkedList();
        }

        // 2. Ambil data lagu (menggunakan method yang ada di class SinglyLinkedList)
        // Pastikan function getAllSongs() sudah kamu buat di file SinglyLinkedList.php
        $songs = $playlist->getAllSongs();

        // 3. Tampilkan hasilnya dalam bentuk JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Data Playlist (Linked List)',
            'total_songs' => $playlist->count,
            'data' => $songs
        ]);
    }

    /**
     * Menambah Lagu Baru ke Playlist
     * URL: /playlist/add
     */
    public function addSong(Request $request)
    {
        // 1. Ambil state Playlist terakhir dari session
        if ($request->session()->has('my_playlist')) {
            $playlist = unserialize($request->session()->get('my_playlist'));
        } else {
            $playlist = new SinglyLinkedList();
        }

        // 2. Buat data Dummy (Pura-pura data lagu)
        // Nanti bisa diganti dengan inputan asli: $request->input('title')
        $newSong = [
            'id' => uniqid(),
            'title' => 'Lagu Percobaan ' . rand(1, 100),
            'artist' => 'Zanify Artist',
            'album' => 'Album Struktur Data',
            'duration' => '3:' . rand(10, 59)
        ];

        // 3. Panggil fungsi Linked List kamu untuk menambah Node
        $playlist->addSong($newSong);

        // 4. Simpan kembali Object Linked List yang sudah berubah ke session
        // Kita pakai serialize() karena Session hanya bisa simpan string/text
        $request->session()->put('my_playlist', serialize($playlist));

        return response()->json([
            'status' => 'success',
            'message' => 'Lagu berhasil ditambahkan ke Linked List!',
            'added_node' => $newSong
        ]);
    }
}