<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
    ];

    /**
     * Get all songs by this artist
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
}
