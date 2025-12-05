<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataStructures\CircularDoublyLinkedList; 
use App\DataStructures\DoublyLinkedList;
use App\DataStructures\Stack; 
use App\DataStructures\Queue;

class PlaylistController extends Controller
{
    // --- HELPER FUNCTIONS ---

    /**
     * Helper: Memuat Struktur Data dari Session (DENGAN PENANGANAN ERROR UNPARSE)
     */
    private function loadStructure(Request $request, $key, $defaultObject)
    {
        if ($request->session()->has($key)) {
            $serialized = $request->session()->get($key);
            
            // PENTING: Coba unserialize. Jika gagal, kembalikan objek baru (kosong).
            try {
                 // Tambahkan parameter allowed_classes=true untuk mengatasi masalah unserialize modern
                 $unserializedObject = unserialize($serialized, ['allowed_classes' => true]);

                 // Cek jika hasil unserialize valid dan sesuai tipe
                 if ($unserializedObject instanceof $defaultObject) {
                    return $unserializedObject;
                 } else {
                    // Jika tipe objek tidak cocok (misal, struktur lama), buang dan mulai baru
                    return $defaultObject;
                 }
            } catch (\Exception $e) {
                 // Jika terjadi error saat unserialize, kembalikan objek default (kosong)
                 return $defaultObject;
            }
        }
        return $defaultObject;
    }

    private function findSongInLibrary(DoublyLinkedList $library, $songId) {
        $current = $library->head;
        while ($current !== null) {
            if (isset($current->data['id']) && $current->data['id'] === $songId) {
                return $current->data;
            }
            $current = $current->next;
        }
        return null;
    }
    
    // -------------------------

    public function index(Request $request)
    {
        $library  = $this->loadStructure($request, 'library', new DoublyLinkedList());
        $playlist = $this->loadStructure($request, 'my_playlist', new CircularDoublyLinkedList());
        $history  = $this->loadStructure($request, 'my_history', new Stack());
        $queue    = $this->loadStructure($request, 'my_queue', new Queue());
        
        $songs = [];
        foreach ($playlist->getAllSongs() as $playlistSong) {
            if (isset($playlistSong['id'])) {
                $songDetails = $this->findSongInLibrary($library, $playlistSong['id']);
                if ($songDetails) {
                    $songs[] = $songDetails;
                }
            }
        }

        return view('playlist', [
            'songs'   => $songs,
            'history' => $history->getHistory(),
            'queue'   => $queue->getQueue(),
            'total'   => count($songs)
        ]);
    }
    
    public function addSong(Request $request)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        $playlist = $this->loadStructure($request, 'my_playlist', new CircularDoublyLinkedList());

        $librarySongs = $library->getAllSongs();
        if (empty($librarySongs)) {
             return response()->json(['status' => 'error', 'message' => 'Library Admin kosong.']);
        }

        $randomSong = $librarySongs[array_rand($librarySongs)];
        $playlist->addSong(['id' => $randomSong['id']]); 
        
        $request->session()->put('my_playlist', serialize($playlist));

        return response()->json(['status' => 'success', 'message' => 'Lagu ditambahkan ke Playlist!', 'data' => $randomSong]);
    }

    public function addToQueue(Request $request)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        $queue = $this->loadStructure($request, 'my_queue', new Queue());
        
        $librarySongs = $library->getAllSongs();
        if (empty($librarySongs)) {
             return response()->json(['status' => 'error', 'message' => 'Library Admin kosong.']);
        }
        
        $randomSong = $librarySongs[array_rand($librarySongs)];

        $queue->enqueue($randomSong);

        $request->session()->put('my_queue', serialize($queue));

        return response()->json(['status' => 'success', 'message' => 'Lagu masuk antrian (Queue)!', 'data' => $randomSong]);
    }
    
    /**
     * FUNGSI: Clear Queue (Mengosongkan Antrian)
     */
    public function clearQueue(Request $request)
    {
        $queue = $this->loadStructure($request, 'my_queue', new Queue());
        $queue->clear();
        
        $request->session()->put('my_queue', serialize($queue));

        return response()->json([
            'status' => 'success',
            'message' => 'Antrian berhasil dikosongkan.',
        ]);
    }

    /**
     * FUNGSI: Pindahkan Item di Queue (Reordering)
     */
    public function moveQueueItem(Request $request)
    {
        $index = (int)$request->input('index');
        $direction = $request->input('direction');

        $queue = $this->loadStructure($request, 'my_queue', new Queue());
        
        $isMoved = $queue->moveItem($index, $direction);

        if ($isMoved) {
            $request->session()->put('my_queue', serialize($queue));
            return response()->json(['status' => 'success', 'message' => 'Urutan lagu berhasil diubah.']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Gagal memindahkan lagu. (Index tidak valid).']);
        }
    }


    public function playSong(Request $request)
    {
        $library = $this->loadStructure($request, 'library', new DoublyLinkedList());
        $history = $this->loadStructure($request, 'my_history', new Stack());

        $librarySongs = $library->getAllSongs();
        if (empty($librarySongs)) {
             return response()->json(['status' => 'error', 'message' => 'Library Admin kosong.']);
        }
        
        $playedSong = $librarySongs[array_rand($librarySongs)];
        $playedSong['played_at'] = date('H:i');

        $history->push($playedSong);

        $request->session()->put('my_history', serialize($history));

        return response()->json(['status' => 'success', 'message' => 'Lagu diputar & masuk History (Stack)!', 'data' => $playedSong]);
    }
}