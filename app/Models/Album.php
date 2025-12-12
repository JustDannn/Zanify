<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist_id',
        'cover',
        'year',
        'release_date',
    ];

    protected $casts = [
        'year' => 'integer',
        'release_date' => 'date',
    ];

    /**
     * Get the artist that owns the album
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Get all songs in this album
     */
    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Build Azure URL directly (FAST - no HTTP call)
     */
    private function buildAzureUrl(string $path): string
    {
        $endpoint = env('AZURE_STORAGE_ENDPOINT', 'https://zanify.blob.core.windows.net');
        $container = env('AZURE_STORAGE_CONTAINER', 'zanifycontainer');
        return rtrim($endpoint, '/') . '/' . $container . '/' . ltrim($path, '/');
    }

    /**
     * Get cover URL - OPTIMIZED: No Azure exists() check
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover) {
            // Full URL - return as-is
            if (str_starts_with($this->cover, 'http')) {
                return $this->cover;
            }
            
            // Build Azure URL directly (no exists check!)
            return $this->buildAzureUrl($this->cover);
        }
        
        return 'https://via.placeholder.com/200x200/1a1a1a/666?text=Album';
    }

    /**
     * Get artist name
     */
    public function getArtistNameAttribute(): string
    {
        return $this->artist?->name ?? 'Unknown Artist';
    }

    /**
     * Get song count
     */
    public function getSongCountAttribute(): int
    {
        return $this->songs()->count();
    }
}
