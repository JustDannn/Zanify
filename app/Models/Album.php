<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
     * Get cover URL
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->cover) {
            if (str_starts_with($this->cover, 'http')) {
                return $this->cover;
            }
            
            try {
                if (Storage::disk('azure')->exists($this->cover)) {
                    $endpoint = config('filesystems.disks.azure.endpoint') ?? env('AZURE_STORAGE_ENDPOINT');
                    $container = config('filesystems.disks.azure.container') ?? env('AZURE_STORAGE_CONTAINER', 'music');
                    return rtrim($endpoint, '/') . '/' . $container . '/' . $this->cover;
                }
            } catch (\Exception $e) {
                // Azure not available
            }
            
            if (Storage::disk('public')->exists($this->cover)) {
                return Storage::disk('public')->url($this->cover);
            }
            
            return Storage::url($this->cover);
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
