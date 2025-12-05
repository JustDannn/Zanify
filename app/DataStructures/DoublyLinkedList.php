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
     * Hapus Lagu dari DLL berdasarkan ID Lagu (CRUD: Delete)
     * Logika: Mencari node dan memutus sambungan prev/next.
     */
    public function deleteSongById($id)
    {
        $current = $this->head;
        while ($current !== null) {
            if ($current->data['id'] === $id) {
                // Kasus 1: Node adalah Head
                if ($current === $this->head) {
                    $this->head = $current->next;
                    if ($this->head !== null) {
                        $this->head->prev = null;
                    }
                }
                // Kasus 2: Node adalah Tail
                elseif ($current === $this->tail) {
                    $this->tail = $current->prev;
                    $this->tail->next = null;
                }
                // Kasus 3: Node di Tengah
                else {
                    $current->prev->next = $current->next;
                    $current->next->prev = $current->prev;
                }
                
                $this->count--;
                // Penting: Memastikan Node tidak lagi di memori (optional)
                $current->next = null; 
                $current->prev = null;

                return true;
            }
            $current = $current->next;
        }
        return false; // ID tidak ditemukan
    }

    /**
     * Menampilkan semua lagu (Traversal Maju)
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
     * Mencari Node berdasarkan ID (Berguna untuk Sinkronisasi)
     */
    public function findSongById($id)
    {
        $current = $this->head;
        while ($current !== null) {
            if ($current->data['id'] === $id) {
                return $current->data;
            }
            $current = $current->next;
        }
        return null;
    }
}