<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Song;
use App\Models\Album;
use App\Models\Artist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class CardGroup extends Component
{
    public $cards = [];
    public $greeting = '';

    public function mount()
    {
        $this->setGreeting();
        $this->loadCards();
    }

    /**
     * Set greeting based on time of day
     */
    private function setGreeting()
    {
        $hour = Carbon::now()->hour;
        
        if ($hour >= 5 && $hour < 12) {
            $this->greeting = 'Good Morning';
        } elseif ($hour >= 12 && $hour < 17) {
            $this->greeting = 'Good Afternoon';
        } elseif ($hour >= 17 && $hour < 21) {
            $this->greeting = 'Good Evening';
        } else {
            $this->greeting = 'Good Night';
        }
    }

    /**
     * Load dynamic cards - mix of recently played, liked, and popular
     */
    private function loadCards()
    {
        $this->cards = [];
        
        // 1. Liked Songs card for authenticated users
        if (Auth::check()) {
            $likedCount = Auth::user()->likedSongs()->count();
            $this->cards[] = [
                'title' => 'Liked Songs',
                'subtitle' => $likedCount . ' songs',
                'image' => null,
                'type' => 'liked',
                'route' => 'liked-songs',
                'has_play_button' => true,
            ];
            
            // 2. Recently Played (if user has history)
            $recentSong = Auth::user()->recentlyPlayed()->with('artists')->first();
            if ($recentSong) {
                $this->cards[] = [
                    'title' => 'Recently Played',
                    'subtitle' => $recentSong->title,
                    'image' => $recentSong->cover_url,
                    'type' => 'playlist',
                    'route' => 'recently-played',
                    'has_play_button' => true,
                ];
            }
        }
        
        // 3. Popular Albums (top 3 by song play count)
        $popularAlbums = Album::with('artist')
            ->withSum('songs', 'play_count')
            ->orderByDesc('songs_sum_play_count')
            ->take(3)
            ->get();
            
        foreach ($popularAlbums as $album) {
            $this->cards[] = [
                'title' => $album->title,
                'subtitle' => $album->artist->name ?? 'Various Artists',
                'image' => $album->cover_url,
                'type' => 'album',
                'id' => $album->id,
                'has_play_button' => true,
            ];
        }
        
        // 4. Top Artists (by song count or listeners)
        $topArtists = Artist::withCount('songs')
            ->orderByDesc('songs_count')
            ->take(2)
            ->get();
            
        foreach ($topArtists as $artist) {
            $this->cards[] = [
                'title' => $artist->name,
                'subtitle' => 'Artist',
                'image' => $artist->photo_url,
                'type' => 'artist',
                'id' => $artist->id,
                'has_play_button' => false,
            ];
        }
        
        // Limit to 8 cards max
        $this->cards = array_slice($this->cards, 0, 8);
    }

    public function render()
    {
        return view('livewire.card-group');
    }
}