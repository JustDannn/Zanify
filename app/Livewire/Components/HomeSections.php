<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class HomeSections extends Component
{
    public $sections = [];
    public $recommendedStations = [];

    public function mount()
    {
        $this->loadSections();
        $this->loadRecommendedStations();
    }

    /**
     * Load all dynamic sections for home page - WITH CACHING
     */
    private function loadSections()
    {
        // Cache sections for 5 minutes
        $this->sections = Cache::remember('home_sections', 300, function () {
            $sections = [];

            // 1. New Releases (Albums/Singles only)
            $sections[] = $this->getNewReleasesSection();

            // 2. Featured Artists
            $sections[] = $this->getFeaturedArtistsSection();

            // 3. Albums to Explore
            $sections[] = $this->getAlbumsSection();

            // Filter out empty sections
            return array_filter($sections, fn($s) => !empty($s['items']));
        });
    }

    /**
     * Load recommended stations - WITH CACHING
     */
    private function loadRecommendedStations()
    {
        $userId = Auth::id() ?? 'guest';
        
        // Cache per user for 5 minutes
        $this->recommendedStations = Cache::remember("stations_{$userId}", 300, function () {
            $stations = [];

            // Get popular artists to create stations from
            $popularArtists = Artist::withCount('songs')
                ->whereHas('songs')
                ->orderByDesc('songs_count')
                ->take(10)
                ->get();

            // If user is logged in, prioritize artists from their listening history
            if (Auth::check()) {
                $recentArtists = Auth::user()->recentlyPlayed()
                    ->with('artists')
                    ->take(20)
                    ->get()
                    ->pluck('artists')
                    ->flatten()
                    ->unique('id')
                    ->take(6);

                if ($recentArtists->isNotEmpty()) {
                    $popularArtists = $recentArtists->merge($popularArtists)->unique('id')->take(10);
                }
            }

            foreach ($popularArtists->take(6) as $artist) {
                $relatedArtistIds = Song::whereHas('artists', fn($q) => $q->where('artists.id', $artist->id))
                    ->with('artists')
                    ->get()
                    ->pluck('artists')
                    ->flatten()
                    ->where('id', '!=', $artist->id)
                    ->unique('id')
                    ->pluck('id')
                    ->take(5);

                $relatedArtists = Artist::whereIn('id', $relatedArtistIds)->get();
                
                $withArtists = $relatedArtists->pluck('name')->take(3)->join(', ');
                if ($relatedArtists->count() > 3) {
                    $withArtists .= '...';
                }

                $stations[] = [
                    'id' => $artist->id,
                    'title' => $artist->name,
                    'subtitle' => $withArtists ? 'With ' . $withArtists : 'Artist Radio',
                    'image' => $artist->photo_url,
                    'artist_ids' => $relatedArtistIds->prepend($artist->id)->toArray(),
                ];
            }
            
            return $stations;
        });
    }

    /**
     * New releases - Albums & Singles only (not individual songs)
     */
    private function getNewReleasesSection(): array
    {
        $albums = Album::with('artist')
            ->withCount('songs')
            ->whereHas('songs')
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->map(fn($album) => [
                'id' => $album->id,
                'title' => $album->title,
                'subtitle' => $album->artist->name ?? 'Various Artists',
                'image' => $album->cover_url,
                'type' => 'album',
                'year' => $album->year ?? now()->year,
                'is_new' => $album->created_at->diffInDays(now()) < 14,
                'songs_count' => $album->songs_count,
            ])
            ->toArray();

        return [
            'title' => 'New Releases',
            'type' => 'albums',
            'items' => $albums,
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
            ->skip(10) // Skip the ones shown in New Releases
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
     * Play a station (radio) - plays songs from selected artist and related artists
     */
    public function playStation(int $artistId, array $artistIds = [])
    {
        // Get songs from these artists
        $songs = Song::whereHas('artists', fn($q) => $q->whereIn('artists.id', $artistIds))
            ->inRandomOrder()
            ->take(50)
            ->pluck('id')
            ->toArray();

        if (!empty($songs)) {
            $artist = Artist::find($artistId);
            $this->dispatch('set-play-source', 
                sourceName: ($artist->name ?? 'Artist') . ' Radio', 
                songIds: $songs, 
                startFromSongId: $songs[0]
            );
            $this->dispatch('play-song', songId: $songs[0]);
        }
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

    /**
     * Play all songs from an album
     */
    public function playAlbum(int $albumId)
    {
        $album = Album::with('songs')->find($albumId);
        
        if ($album && $album->songs->isNotEmpty()) {
            $songIds = $album->songs->pluck('id')->toArray();
            $this->dispatch('set-play-source', 
                sourceName: $album->title, 
                songIds: $songIds, 
                startFromSongId: $songIds[0]
            );
            $this->dispatch('play-song', songId: $songIds[0]);
        }
    }

    public function render()
    {
        return view('livewire.components.home-sections');
    }
}
