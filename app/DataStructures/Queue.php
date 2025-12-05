<?php

namespace App\DataStructures;

use App\DataStructures\Node;

/**
 * Implementasi Struktur Data Tambahan (Nilai Plus)
 * Sesuai BAB III - Poin 3.4: Queue (Antrian)
 * * Digunakan untuk fitur "Add to Queue" (memutar lagu sementara tanpa masuk playlist utama).
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
     * Sesuai logika Add to Queue.
     */
    public function enqueue($data)
    {
        $newNode = new Node($data);

        // Jika antrian kosong, Front & Rear menunjuk ke node baru
        if ($this->rear === null) {
            $this->front = $newNode;
            $this->rear = $newNode;
        } else {
            // Sambungkan Rear saat ini ke Node baru
            $this->rear->next = $newNode;
            // Pindahkan pointer Rear ke Node baru
            $this->rear = $newNode;
        }
        $this->count++;
    }

    /**
     * Dequeue: Keluar antrian (Ambil Node dari Front).
     * Sesuai logika memutar lagu "Next Up".
     */
    public function dequeue()
    {
        if ($this->front === null) {
            return null;
        }

        // Simpan data node depan sementara
        $temp = $this->front;
        
        // Geser Front ke node berikutnya
        $this->front = $this->front->next;

        // Jika setelah digeser Front jadi null (kosong), Rear juga harus null
        if ($this->front === null) {
            $this->rear = null;
        }
        
        $this->count--;

        // Opsional: Putus koneksi node lama (Clean up reference)
        $temp->next = null; 

        return $temp->data;
    }

    /**
     * Peek: Melihat data antrian paling depan tanpa menghapusnya.
     * Berguna untuk menampilkan info "Next Song" di UI Player.
     */
    public function peek()
    {
        if ($this->front === null) {
            return null;
        }
        return $this->front->data;
    }

    /**
     * Cek apakah Queue kosong.
     */
    public function isEmpty()
    {
        return $this->front === null;
    }

    /**
     * Mengubah Queue jadi Array untuk ditampilkan di Frontend/View.
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
}