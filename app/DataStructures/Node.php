<?php

namespace App\DataStructures;

class Node
{
    public $data;
    public $next; // Pointer ke node setelahnya
    public $prev; // Pointer ke node sebelumnya (Baru: Untuk Doubly LL)

    public function __construct($data)
    {
        $this->data = $data;
        $this->next = null;
        $this->prev = null;
    }
}