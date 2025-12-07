<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

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
     * Get the cover URL
     * If song belongs to an album, use album cover instead
     */
    public function getCoverUrlAttribute(): string
    {
        // If song belongs to an album, use album's cover
        if ($this->album_id && $this->album && $this->album->cover) {
            return $this->album->cover_url;
        }

        // Otherwise use song's own cover
        if ($this->cover) {
            // Check if it's a full URL already
            if (str_starts_with($this->cover, 'http')) {
                return $this->cover;
            }
            
            // Try Azure storage first - generate proper Azure URL
            try {
                if (Storage::disk('azure')->exists($this->cover)) {
                    // Build Azure URL manually
                    $endpoint = config('filesystems.disks.azure.endpoint') ?? env('AZURE_STORAGE_ENDPOINT');
                    $container = config('filesystems.disks.azure.container') ?? env('AZURE_STORAGE_CONTAINER', 'music');
                    return rtrim($endpoint, '/') . '/' . $container . '/' . $this->cover;
                }
            } catch (\Exception $e) {
                // Azure not available, try local
            }
            
            // Local storage
            if (Storage::disk('public')->exists($this->cover)) {
                return Storage::disk('public')->url($this->cover);
            }
            
            // Fallback - return as-is
            return Storage::url($this->cover);
        }
        
        // Default placeholder
        return 'https://via.placeholder.com/100x100/1a1a1a/666?text=♪';
    }

    /**
     * Get the audio file URL from Azure Blob Storage
     */
    public function getAudioUrlAttribute(): ?string
    {
        if (!$this->audio_path) {
            return null;
        }

        // Check if it's a full URL already
        if (str_starts_with($this->audio_path, 'http')) {
            return $this->audio_path;
        }

        // Build Azure URL
        try {
            $endpoint = config('filesystems.disks.azure.endpoint') ?? env('AZURE_STORAGE_ENDPOINT');
            $container = config('filesystems.disks.azure.container') ?? env('AZURE_STORAGE_CONTAINER', 'music');
            return rtrim($endpoint, '/') . '/' . $container . '/' . $this->audio_path;
        } catch (\Exception $e) {
            return null;
        }
    }
}
