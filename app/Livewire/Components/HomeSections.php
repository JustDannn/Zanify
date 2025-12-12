<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class HomeSections extends Component
{
    public $sections = [];

    public function mount()
    {
        $this->loadSections();
    }

    /**
     * Load all dynamic sections for home page
     */
    private function loadSections()
    {
        $this->sections = [];

        // 1. Popular Songs This Week
        $this->sections[] = $this->getPopularSongsSection();

        // 2. New Releases
        $this->sections[] = $this->getNewReleasesSection();

        // 3. Featured Artists
        $this->sections[] = $this->getFeaturedArtistsSection();

        // 4. Albums to Explore
        $this->sections[] = $this->getAlbumsSection();

        // 5. Based on your listening (for authenticated users)
        if (Auth::check()) {
            $recentSection = $this->getBasedOnListeningSection();
            if ($recentSection) {
                array_unshift($this->sections, $recentSection); // Add to beginning
            }
        }

        // Filter out empty sections
        $this->sections = array_filter($this->sections, fn($s) => !empty($s['items']));
    }

    /**
     * Popular songs this week
     */
    private function getPopularSongsSection(): array
    {
        $songs = Song::with(['artists', 'album'])
            ->orderByDesc('play_count')
            ->take(10)
            ->get()
            ->map(fn($song) => [
                'id' => $song->id,
                'title' => $song->title,
                'subtitle' => $song->artist_display,
                'image' => $song->cover_url,
                'type' => 'song',
                'play_count' => $song->play_count,
            ])
            ->toArray();

        return [
            'title' => 'Popular This Week',
            'type' => 'songs',
            'items' => $songs,
        ];
    }

    /**
     * New releases (recently added songs/albums)
     */
    private function getNewReleasesSection(): array
    {
        $songs = Song::with(['artists', 'album'])
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(fn($song) => [
                'id' => $song->id,
                'title' => $song->title,
                'subtitle' => $song->artist_display,
                'image' => $song->cover_url,
                'type' => 'song',
                'is_new' => $song->created_at->diffInDays(now()) < 7,
            ])
            ->toArray();

        return [
            'title' => 'New Releases',
            'type' => 'songs',
            'items' => $songs,
        ];
    }

    /**
     * Featured Artists
     */
    private function getFeaturedArtistsSection(): array
    {
        $artists = Artist::withCount('songs')
            ->whereHas('songs')
            ->orderByDesc('songs_count')
            ->take(8)
            ->get()
            ->map(fn($artist) => [
                'id' => $artist->id,
                'title' => $artist->name,
                'subtitle' => $artist->songs_count . ' songs',
                'image' => $artist->photo_url,
                'type' => 'artist',
                'is_rounded' => true, // Artists have circular images
            ])
            ->toArray();

        return [
            'title' => 'Featured Artists',
            'type' => 'artists',
            'items' => $artists,
        ];
    }

    /**
     * Albums to explore
     */
    private function getAlbumsSection(): array
    {
        $albums = Album::with('artist')
            ->withCount('songs')
            ->whereHas('songs')
            ->orderByDesc('created_at')
            ->take(8)
            ->get()
            ->map(fn($album) => [
                'id' => $album->id,
                'title' => $album->title,
                'subtitle' => $album->artist->name ?? 'Various Artists',
                'image' => $album->cover_url,
                'type' => 'album',
                'year' => $album->year,
            ])
            ->toArray();

        return [
            'title' => 'Albums',
            'type' => 'albums',
            'items' => $albums,
        ];
    }

    /**
     * Based on user's recent listening
     */
    private function getBasedOnListeningSection(): ?array
    {
        if (!Auth::check()) return null;

        $recentArtists = Auth::user()->recentlyPlayed()
            ->with('artists')
            ->take(5)
            ->get()
            ->pluck('artists')
            ->flatten()
            ->unique('id')
            ->take(3);

        if ($recentArtists->isEmpty()) return null;

        // Get songs from similar artists
        $artistIds = $recentArtists->pluck('id');
        $artistNames = $recentArtists->pluck('name')->take(2)->join(' & ');

        $songs = Song::with(['artists', 'album'])
            ->whereHas('artists', fn($q) => $q->whereIn('artists.id', $artistIds))
            ->whereNotIn('id', Auth::user()->recentlyPlayed()->pluck('songs.id'))
            ->inRandomOrder()
            ->take(10)
            ->get()
            ->map(fn($song) => [
                'id' => $song->id,
                'title' => $song->title,
                'subtitle' => $song->artist_display,
                'image' => $song->cover_url,
                'type' => 'song',
            ])
            ->toArray();

        if (empty($songs)) return null;

        return [
            'title' => 'More Like ' . $artistNames,
            'type' => 'songs',
            'items' => $songs,
        ];
    }

    /**
     * Play a song from section
     */
    public function playSong(int $songId, array $sectionSongIds = [])
    {
        if (!empty($sectionSongIds)) {
            $this->dispatch('set-play-source', 
                sourceName: 'Home', 
                songIds: $sectionSongIds, 
                startFromSongId: $songId
            );
        }
        $this->dispatch('play-song', songId: $songId);
    }

    public function render()
    {
        return view('livewire.components.home-sections');
    }
}
