<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// GANTI: Gunakan Circular Doubly Linked List untuk Playlist User
use App\DataStructures\CircularDoublyLinkedList; 
use App\DataStructures\Stack; 
use App\DataStructures\Queue;

class PlaylistController extends Controller
{
    /**
     * Menampilkan Halaman Web Playlist (Frontend)
     * URL: /playlist
     */
    public function index(Request $request)
    {
        // 1. Load Struktur Data dari Session
        // PERUBAHAN: Default object sekarang adalah CircularDoublyLinkedList
        $playlist = $this->loadFromSession($request, 'my_playlist', new CircularDoublyLinkedList());
        $history  = $this->loadFromSession($request, 'my_history', new Stack());
        $queue    = $this->loadFromSession($request, 'my_queue', new Queue());

        // 2. Ambil data array untuk dikirim ke View
        $songs = $playlist->getAllSongs();
        $historySongs = $history->getHistory(); 
        $queueSongs = $queue->getQueue();      

        // 3. Return View
        return view('playlist', [
            'songs'   => $songs,
            'history' => $historySongs,
            'queue'   => $queueSongs,  
            'total'   => $playlist->count
        ]);
    }

    /**
     * Menambah Lagu Baru ke Playlist (Circular DLL)
     * URL: /playlist/add
     */
    public function addSong(Request $request)
    {
        // Load sebagai CircularDoublyLinkedList
        $playlist = $this->loadFromSession($request, 'my_playlist', new CircularDoublyLinkedList());
        
        $newSong = $this->generateRandomSong();

        // Masukkan ke Playlist (Circular Logic sudah ada di class-nya)
        $playlist->addSong($newSong);
        
        // Simpan state terbaru
        $request->session()->put('my_playlist', serialize($playlist));

        return response()->json([
            'status' => 'success',
            'message' => 'Lagu ditambahkan ke Playlist (Circular DLL)!',
            'data' => $newSong
        ]);
    }

    /**
     * Menambah Lagu ke Antrian Next Up (Queue - FIFO)
     * URL: /playlist/queue
     */
    public function addToQueue(Request $request)
    {
        $queue = $this->loadFromSession($request, 'my_queue', new Queue());
        
        $newSong = $this->generateRandomSong();
        $newSong['source'] = 'Queue Request';

        $queue->enqueue($newSong);

        $request->session()->put('my_queue', serialize($queue));

        return response()->json([
            'status' => 'success',
            'message' => 'Lagu masuk antrian (Queue)!',
            'data' => $newSong
        ]);
    }

    /**
     * Simulasi Memutar Lagu (Masuk History Stack)
     * URL: /playlist/play
     */
    public function playSong(Request $request)
    {
        $history = $this->loadFromSession($request, 'my_history', new Stack());

        $playedSong = $this->generateRandomSong();
        $playedSong['played_at'] = date('H:i');

        $history->push($playedSong);

        $request->session()->put('my_history', serialize($history));

        return response()->json([
            'status' => 'success',
            'message' => 'Lagu diputar & masuk History (Stack)!',
            'data' => $playedSong
        ]);
    }

    // --- HELPER FUNCTIONS ---

    private function loadFromSession(Request $request, $key, $defaultObject)
    {
        if ($request->session()->has($key)) {
            // Unserialize mengubah string kembali menjadi Object
            // Pastikan class CircularDoublyLinkedList sudah ada di namespace yang benar
            return unserialize($request->session()->get($key));
        }
        return $defaultObject;
    }

    private function generateRandomSong()
    {
        $artists = ['Tulus', 'Coldplay', 'Arctic Monkeys', 'Nadin Amizah', 'Bruno Mars', 'Sheila On 7', 'Bernadya', 'Payung Teduh'];
        $titles = ['Hati-Hati di Jalan', 'Yellow', 'Do I Wanna Know?', 'Bertaut', 'Talking to the Moon', 'Dan', 'Satu Bulan', 'Akad'];
        $randIndex = array_rand($artists);

        return [
            'id' => uniqid(),
            'title' => $titles[$randIndex],
            'artist' => $artists[$randIndex],
            'album' => 'Zanify Hits',
            'date_added' => date('M d, Y'),
            'duration' => rand(2, 5) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT)
        ];
    }
}