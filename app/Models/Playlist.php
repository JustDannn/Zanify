<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover_image',
    ];

    /**
     * Get the user that owns the playlist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the songs in the playlist.
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'playlist_song')
            ->withPivot('order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * Get the number of songs in the playlist.
     */
    public function getSongsCountAttribute(): int
    {
        return $this->songs()->count();
    }
}
