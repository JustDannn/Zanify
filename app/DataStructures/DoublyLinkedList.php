<?php

namespace App\DataStructures;

use App\DataStructures\Node;

/**
 * Implementasi BAB III - Poin 3.1: Struktur Data Utama (Library Lagu)
 * Jenis: Doubly Linked List (DLL)
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

    public function addSong($data)
    {
        $newNode = new Node($data);

        if ($this->head === null) {
            $this->head = $newNode;
            $this->tail = $newNode;
        } else {
            $this->tail->next = $newNode;
            $newNode->prev = $this->tail;
            $this->tail = $newNode;
        }
        $this->count++;
    }

    // --- FITUR BARU: FIND & UPDATE ---

    /**
     * Mencari Node berdasarkan ID
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

    /**
     * Update data lagu berdasarkan ID
     */
    public function updateSong($id, $newData)
    {
        $current = $this->head;
        while ($current !== null) {
            if ($current->data['id'] === $id) {
                // Gabungkan data lama dengan data baru (agar ID dan Audio URL aman jika tidak diubah)
                // array_merge menimpa data lama dengan data baru yang memiliki key sama
                $current->data = array_merge($current->data, $newData);
                return true;
            }
            $current = $current->next;
        }
        return false;
    }

    // ---------------------------------

    public function deleteSongById($id)
    {
        $current = $this->head;
        while ($current !== null) {
            if ($current->data['id'] === $id) {
                if ($current === $this->head) {
                    $this->head = $current->next;
                    if ($this->head !== null) {
                        $this->head->prev = null;
                    }
                } elseif ($current === $this->tail) {
                    $this->tail = $current->prev;
                    $this->tail->next = null;
                } else {
                    $current->prev->next = $current->next;
                    $current->next->prev = $current->prev;
                }
                
                $this->count--;
                $current->next = null; 
                $current->prev = null;
                return true;
            }
            $current = $current->next;
        }
        return false;
    }

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