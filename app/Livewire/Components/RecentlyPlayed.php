<?php

namespace App\Livewire\Components;

use App\Models\Song;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Livewire\Traits\WithPlaylistActions;

class RecentlyPlayed extends Component
{
    use WithPlaylistActions;

    public $groupedSongs = [];
    public $totalCount = 0;

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $this->loadRecentlyPlayed();
    }

    #[On('recently-played-updated')]
    public function onRecentlyPlayedUpdated()
    {
        $this->loadRecentlyPlayed();
    }

    public function loadRecentlyPlayed()
    {
        if (!Auth::check()) {
            $this->groupedSongs = [];
            $this->totalCount = 0;
            return;
        }

        // Get songs from last 7 days only
        $sevenDaysAgo = now()->subDays(7)->startOfDay();
        
        $songs = Auth::user()
            ->recentlyPlayed()
            ->with('artists')
            ->wherePivot('played_at', '>=', $sevenDaysAgo)
            ->get();

        $this->totalCount = $songs->count();

        // Group by date
        $grouped = $songs->groupBy(function ($song) {
            $playedAt = Carbon::parse($song->pivot->played_at);
            
            if ($playedAt->isToday()) {
                return 'Hari Ini';
            } elseif ($playedAt->isYesterday()) {
                return 'Kemarin';
            } else {
                return $playedAt->translatedFormat('l, d F Y');
            }
        });

        $this->groupedSongs = $grouped->map(function ($songs, $date) {
            return [
                'date' => $date,
                'songs' => $songs->map(function ($song) {
                    return [
                        'id' => $song->id,
                        'title' => $song->title,
                        'artist' => $song->artist_display,
                        'cover' => $song->cover_url,
                        'duration_formatted' => $song->duration_formatted,
                        'album' => $song->album?->title ?? 'Single',
                        'played_at' => Carbon::parse($song->pivot->played_at)->format('H:i'),
                    ];
                })->toArray(),
            ];
        })->values()->toArray();
    }

    public function playSong(int $songId)
    {
        // Find the song data in our grouped structure
        $songData = null;
        foreach ($this->groupedSongs as $group) {
            foreach ($group['songs'] as $song) {
                if ($song['id'] === $songId) {
                    $songData = $song;
                    break 2;
                }
            }
        }
        
        if (!$songData) return;
        
        // Flatten all songs for autoplay source
        $allSongIds = collect($this->groupedSongs)
            ->flatMap(fn($group) => collect($group['songs'])->pluck('id'))
            ->toArray();
        
        $this->dispatch('set-play-source', sourceName: 'Recently Played', songIds: $allSongIds, startFromSongId: $songId);
        
        // We need to get the audio URL - load song from DB (but this is already loaded)
        $song = Song::with('album')->find($songId);
        if (!$song) return;
        
        $isLiked = Auth::check() ? Auth::user()->hasLiked($song) : false;
        $this->dispatch('play-song-data', songData: [
            'id' => $song->id,
            'title' => $songData['title'],
            'artist' => $songData['artist'],
            'cover' => $songData['cover'],
            'audioUrl' => $song->audio_url,
            'duration' => $song->duration,
            'isLiked' => $isLiked,
        ]);
    }

    /**
     * Add song to queue
     */
    public function addToQueue(int $songId)
    {
        $this->dispatch('add-to-queue', songId: $songId);
    }

    public function clearHistory()
    {
        if (Auth::check()) {
            \DB::table('recently_played')
                ->where('user_id', Auth::id())
                ->delete();
            
            $this->loadRecentlyPlayed();
        }
    }

    public function render()
    {
        return view('livewire.components.recently-played');
    }
}
