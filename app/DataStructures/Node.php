<?php

namespace App\DataStructures;

class Node
{
    public $data; 
    public $next; 

    public function __construct($songData)
    {
        $this->data = $songData;
        $this->next = null;
    }
}