<?php

namespace App\DataStructures;

use App\DataStructures\Node;

/**
 * Implementasi Struktur Data Tambahan (Nilai Plus)
 * Sesuai BAB III - Poin 3.4: Queue (Antrian)
 * Prinsip: FIFO (First In First Out).
 */
class Queue
{
    public $front;
    public $rear;
    public $count;

    public function __construct()
    {
        $this->front = null;
        $this->rear = null;
        $this->count = 0;
    }

    /**
     * Enqueue: Masuk antrian (Tambah Node di belakang Rear).
     */
    public function enqueue($data)
    {
        $newNode = new Node($data);

        if ($this->rear === null) {
            $this->front = $newNode;
            $this->rear = $newNode;
        } else {
            $this->rear->next = $newNode;
            $this->rear = $newNode;
        }
        $this->count++;
    }

    /**
     * Dequeue: Keluar antrian (Ambil Node dari Front).
     */
    public function dequeue()
    {
        if ($this->front === null) {
            return null;
        }

        $temp = $this->front;
        $this->front = $this->front->next;

        if ($this->front === null) {
            $this->rear = null;
        }
        
        $this->count--;
        
        // Penting: Mengembalikan data lagu
        return $temp->data;
    }

    /**
     * FUNGSI: Mengosongkan seluruh antrian.
     */
    public function clear()
    {
        $this->front = null;
        $this->rear = null;
        $this->count = 0;
    }

    /**
     * FUNGSI: Mengambil semua data sebagai Array.
     */
    public function getQueue()
    {
        $queueData = [];
        $current = $this->front;
        while ($current !== null) {
            $queueData[] = $current->data;
            $current = $current->next;
        }
        return $queueData;
    }

    /**
     * FUNGSI: Memindahkan item dalam Antrian (Reordering).
     */
    public function moveItem(int $index, string $direction): bool
    {
        if ($index < 0 || $index >= $this->count || $this->count < 2) {
            return false;
        }
        
        $targetIndex = $index + ($direction === 'up' ? -1 : 1);

        if ($targetIndex < 0 || $targetIndex >= $this->count) {
            return false;
        }
        
        // 1. Ambil data, lakukan pertukaran di Array
        $dataArray = $this->getQueue();
        
        $temp = $dataArray[$index];
        $dataArray[$index] = $dataArray[$targetIndex];
        $dataArray[$targetIndex] = $temp;
        
        // 2. Kosongkan Queue yang lama secara total
        $this->clear();
        
        // 3. Isi kembali Queue dengan urutan yang baru
        foreach ($dataArray as $data) {
            $this->enqueue($data);
        }
        
        return true;
    }
}