<?php

namespace App\DataStructures;

use App\DataStructures\Node;

/**
 * Implementasi BAB III - Poin 3.2: Struktur Data Playlist
 * Jenis: Circular Doubly Linked List (CDLL)
 * Fitur: Next/Prev tanpa henti (Looping).
 * PENTING: Node hanya menyimpan ID Lagu (Simulasi Pointer/Sinkronisasi)
 */
class CircularDoublyLinkedList
{
    public $head;
    public $count;

    public function __construct()
    {
        $this->head = null;
        $this->count = 0;
    }

    /**
     * Menambah lagu ke Playlist User
     * Menerima array dengan format: ['id' => 'song_id']
     */
    public function addSong($data)
    {
        // Pastikan kita hanya menyimpan ID
        $dataToStore = ['id' => $data['id']];
        $newNode = new Node($dataToStore);

        if ($this->head === null) {
            $this->head = $newNode;
            $newNode->next = $newNode; 
            $newNode->prev = $newNode; 
        } else {
            $last = $this->head->prev; 

            $last->next = $newNode;
            $newNode->prev = $last;

            $newNode->next = $this->head;
            $this->head->prev = $newNode;
        }
        $this->count++;
    }

    /**
     * Hapus Lagu dari Playlist CDLL berdasarkan ID Lagu (Wajib untuk Sinkronisasi)
     * INI ADALAH FUNGSI YANG HILANG DAN MENYEBABKAN ERROR 500
     */
    public function deleteSongById($id)
    {
        if ($this->head === null) {
            return false;
        }
        
        $current = $this->head;
        $deleted = false;

        do {
            if ($current->data['id'] === $id) {
                // Kasus 1: Hanya ada 1 Node
                if ($current->next === $current) {
                    $this->head = null;
                } 
                // Kasus 2: Node yang dihapus adalah Head
                else {
                    $current->prev->next = $current->next;
                    $current->next->prev = $current->prev;
                    if ($current === $this->head) {
                        $this->head = $current->next; // Geser Head
                    }
                }
                $this->count--;
                $deleted = true;
                break; // Keluar dari loop setelah menghapus
            }
            $current = $current->next;
        } while ($current !== $this->head); // Loop sampai kembali ke Head

        return $deleted;
    }

    /**
     * Mengambil semua ID lagu untuk sinkronisasi di Controller
     */
    public function getAllSongs()
    {
        if ($this->head === null) return [];

        $songs = [];
        $current = $this->head;

        do {
            $songs[] = $current->data; 
            $current = $current->next;
        } while ($current !== $this->head);

        return $songs;
    }
}