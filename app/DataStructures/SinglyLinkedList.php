<?php

namespace App\DataStructures;

use App\DataStructures\Node;

class SinglyLinkedList
{
    public $head;
    public $count;

    public function __construct()
    {
        $this->head = null;
        $this->count = 0;
    }

    public function addSong($songData)
    {
        $newNode = new Node($songData);

        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
        $this->count++;
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