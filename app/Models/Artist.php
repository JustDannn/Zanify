<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'bio',
        'monthly_listeners',
    ];

    protected $casts = [
        'monthly_listeners' => 'integer',
    ];

    /**
     * Get all songs by this artist
     */
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'song_artist');
    }

    /**
     * Get all albums by this artist
     */
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http')) {
                return $this->photo;
            }
            
            try {
                $endpoint = config('filesystems.disks.azure.endpoint') ?? env('AZURE_STORAGE_ENDPOINT');
                $container = config('filesystems.disks.azure.container') ?? env('AZURE_STORAGE_CONTAINER', 'music');
                return rtrim($endpoint, '/') . '/' . $container . '/' . $this->photo;
            } catch (\Exception $e) {
                // Fallback
            }
        }
        
        // Default placeholder - artist silhouette
        return 'https://via.placeholder.com/200x200/1a1a1a/666?text=' . urlencode(substr($this->name, 0, 1));
    }
}
