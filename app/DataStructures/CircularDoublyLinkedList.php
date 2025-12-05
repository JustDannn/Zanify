<?php

namespace App\DataStructures;

use App\DataStructures\Node;

/**
 * Implementasi BAB III - Poin 3.2: Struktur Data Playlist
 * Jenis: Circular Doubly Linked List (CDLL)
 * Fitur: Next/Prev tanpa henti (Looping).
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
     * Konsep: Insert Last pada Circular DLL
     */
    public function addSong($data)
    {
        $newNode = new Node($data);

        if ($this->head === null) {
            $this->head = $newNode;
            $newNode->next = $newNode; // Menunjuk ke diri sendiri (Circular)
            $newNode->prev = $newNode; // Menunjuk ke diri sendiri (Circular)
        } else {
            $last = $this->head->prev; // Node sebelum head adalah node terakhir

            // Sambungkan Node terakhir ke Node Baru
            $last->next = $newNode;
            $newNode->prev = $last;

            // Sambungkan Node Baru ke Head (Looping)
            $newNode->next = $this->head;
            $this->head->prev = $newNode;
        }
        $this->count++;
    }

    /**
     * Mengambil semua lagu untuk ditampilkan di Frontend
     * Perlu hati-hati agar loop tidak infinite saat mengambil data
     */
    public function getAllSongs()
    {
        if ($this->head === null) return [];

        $songs = [];
        $current = $this->head;

        // Loop menggunakan do-while agar jalan minimal sekali
        do {
            $songs[] = $current->data;
            $current = $current->next;
        } while ($current !== $this->head);

        return $songs;
    }
}