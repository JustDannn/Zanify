<?php

namespace App\DataStructures;

use App\DataStructures\Node;

/**
 * Implementasi BAB III - Poin 3.1: Struktur Data Utama (Library Lagu)
 * Jenis: Doubly Linked List (DLL)
 * Fitur: Traversal dua arah (Maju/Mundur), Insert, Delete efisien.
 */
class DoublyLinkedList
{
    public $head;
    public $tail;
    public $count;

    public function __construct()
    {
        $this->head = null;
        $this->tail = null;
        $this->count = 0;
    }

    /**
     * Menambah lagu ke Library (Insert Last)
     */
    public function addSong($data)
    {
        $newNode = new Node($data);

        if ($this->head === null) {
            $this->head = $newNode;
            $this->tail = $newNode;
        } else {
            $this->tail->next = $newNode; // Tail lama -> Next -> Node Baru
            $newNode->prev = $this->tail; // Node Baru -> Prev -> Tail lama
            $this->tail = $newNode;       // Pindahkan Tail ke Node Baru
        }
        $this->count++;
    }

    /**
     * Menampilkan semua lagu (Traversal Maju)
     * Digunakan Admin untuk melihat Library.
     */
    public function getAllSongs()
    {
        $songs = [];
        $current = $this->head;
        while ($current !== null) {
            $songs[] = $current->data;
            $current = $current->next;
        }
        return $songs;
    }

    /**
     * Mencari Lagu Mirip Berdasarkan Genre (Logic Poin 3.3)
     * Algoritma: Linear Search pada DLL
     */
    public function findSimilarSongs($genre, $excludeTitle = null)
    {
        $similarSongs = [];
        $current = $this->head;
        
        while ($current !== null) {
            // Cek jika genrenya sama DAN bukan lagu yang sedang diputar
            if (
                isset($current->data['genre']) && 
                $current->data['genre'] === $genre &&
                $current->data['title'] !== $excludeTitle
            ) {
                $similarSongs[] = $current->data;
            }
            $current = $current->next;
        }

        // Jika tidak ada yang mirip, kembalikan null atau array kosong
        return $similarSongs;
    }
}DoublyLinkedList.php