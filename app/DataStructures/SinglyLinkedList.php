<?php

namespace App\DataStructures;

use App\DataStructures\Node;

class SinglyLinkedList
{
    public $head;
    public $count; // Untuk menghitung jumlah lagu di playlist

    public function __construct()
    {
        $this->head = null;
        $this->count = 0;
    }

    /**
     * Menambah lagu (Node) ke akhir Linked List.
     * Implementasi: Insert Last
     */
    public function addSong($songData)
    {
        $newNode = new Node($songData);

        // Jika playlist kosong, node baru jadi kepala (head)
        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            // Loop sampai node terakhir (yang $current->next nya null)
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            // Sambungkan node terakhir ke node baru
            $current->next = $newNode;
        }
        $this->count++;
    }

    /**
     * Mengubah semua Node Linked List menjadi Array biasa
     * Agar mudah dibaca oleh Frontend (HTML)
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
}