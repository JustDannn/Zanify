<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'artist_name',
        'album_id',
        'cover',
        'audio_path',
        'duration',
        'play_count',
        'listeners',
        'save_count',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
        'play_count' => 'integer',
        'listeners' => 'integer',
        'save_count' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Get formatted duration (mm:ss)
     */
    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration) {
            return '-';
        }
        
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'song_artist');
    }

    /**
     * Get artist names as comma-separated string
     */
    public function getArtistDisplayAttribute(): string
    {
        // First check if there are related artists
        if ($this->artists->isNotEmpty()) {
            return $this->artists->pluck('name')->join(', ');
        }
        
        // Fallback to artist_name field
        return $this->artist_name ?? 'Unknown Artist';
    }

    /**
     * Build Azure URL directly without checking existence (FAST)
     */
    private function buildAzureUrl(string $path): string
    {
        $endpoint = env('AZURE_STORAGE_ENDPOINT', 'https://zanify.blob.core.windows.net');
        $container = env('AZURE_STORAGE_CONTAINER', 'zanifycontainer');
        return rtrim($endpoint, '/') . '/' . $container . '/' . ltrim($path, '/');
    }

    /**
     * Get the cover URL - OPTIMIZED: No Azure exists() check
     */
    public function getCoverUrlAttribute(): string
    {
        // If song belongs to an album, use album's cover
        if ($this->album_id) {
            // Load album if not already loaded
            $album = $this->relationLoaded('album') ? $this->album : $this->album()->first();
            if ($album?->cover) {
                return $album->cover_url;
            }
        }

        // Use song's own cover
        if ($this->cover) {
            // Full URL - return as-is
            if (str_starts_with($this->cover, 'http')) {
                return $this->cover;
            }
            
            // Build Azure URL directly (no exists check!)
            return $this->buildAzureUrl($this->cover);
        }
        
        // Default placeholder
        return 'https://via.placeholder.com/100x100/1a1a1a/666?text=♪';
    }

    /**
     * Get the audio file URL - OPTIMIZED
     */
    public function getAudioUrlAttribute(): ?string
    {
        if (!$this->audio_path) {
            return null;
        }

        // Full URL - return as-is
        if (str_starts_with($this->audio_path, 'http')) {
            return $this->audio_path;
        }

        // Build Azure URL directly
        return $this->buildAzureUrl($this->audio_path);
    }
}
