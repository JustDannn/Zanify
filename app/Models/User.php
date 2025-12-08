<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Songs liked by the user
     */
    public function likedSongs()
    {
        return $this->belongsToMany(Song::class, 'liked_songs')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    /**
     * Check if user has liked a song
     */
    public function hasLiked(Song $song): bool
    {
        return $this->likedSongs()->where('song_id', $song->id)->exists();
    }

    /**
     * Toggle like on a song
     */
    public function toggleLike(Song $song): bool
    {
        if ($this->hasLiked($song)) {
            $this->likedSongs()->detach($song->id);
            return false;
        } else {
            $this->likedSongs()->attach($song->id);
            return true;
        }
    }

    /**
     * Recently played songs
     */
    public function recentlyPlayed()
    {
        return $this->belongsToMany(Song::class, 'recently_played')
            ->withPivot('played_at')
            ->orderByPivot('played_at', 'desc');
    }

    /**
     * Record a song as played
     */
    public function recordPlay(Song $song): void
    {
        // Remove existing entry if exists (to update played_at)
        $this->recentlyPlayed()->detach($song->id);
        
        // Add new entry with current timestamp
        $this->recentlyPlayed()->attach($song->id, ['played_at' => now()]);
        
        // Delete entries older than 7 days
        \DB::table('recently_played')
            ->where('user_id', $this->id)
            ->where('played_at', '<', now()->subDays(7))
            ->delete();
    }
}
