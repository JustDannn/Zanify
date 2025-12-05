<?php

namespace App\DataStructures;

use App\DataStructures\Node;

class Stack
{
    public $top;
    public $count;

    public function __construct()
    {
        $this->top = null;
        $this->count = 0;
    }

    /**
     * Push: Menambah data ke tumpukan paling atas
     * (Mirip Insert First di Linked List)
     */
    public function push($data)
    {
        $newNode = new Node($data);
        
        if ($this->top === null) {
            $this->top = $newNode;
        } else {
            $newNode->next = $this->top;
            $this->top = $newNode;
        }
        $this->count++;
    }

    /**
     * Pop: Mengambil data paling atas dan menghapusnya
     */
    public function pop()
    {
        if ($this->top === null) {
            return null;
        }

        $temp = $this->top;
        $this->top = $this->top->next;
        $this->count--;

        return $temp->data;
    }

    /**
     * Mengubah Stack jadi Array untuk ditampilkan di Frontend
     */
    public function getHistory()
    {
        $history = [];
        $current = $this->top;
        while ($current !== null) {
            $history[] = $current->data;
            $current = $current->next;
        }
        return $history;
    }
}