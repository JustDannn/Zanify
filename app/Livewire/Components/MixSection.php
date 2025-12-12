<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Song;
use App\Models\Artist;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class MixSection extends Component
{
    public $mixes = [];
    public $sectionTitle = 'Made For You';
    public $userName = '';

    public function mount()
    {
        $this->userName = Auth::check() ? Auth::user()->name : 'You';
        $this->sectionTitle = 'Made For ' . $this->userName;
        $this->generateDailyMixes();
    }

    /**
     * Generate dynamic daily mixes based on available artists
     */
    private function generateDailyMixes()
    {
        $this->mixes = [];
        
        // Get artists with songs, grouped for mixes
        $artists = Artist::withCount('songs')
            ->whereHas('songs')
            ->orderByDesc('songs_count')
            ->take(15)
            ->get();
        
        if ($artists->isEmpty()) {
            // Fallback to placeholder mixes
            $this->mixes = $this->getPlaceholderMixes();
            return;
        }
        
        // Create up to 5 daily mixes, each with 3 artists
        $mixNumber = 1;
        $artistChunks = $artists->chunk(3);
        
        foreach ($artistChunks->take(5) as $chunk) {
            $artistNames = $chunk->pluck('name')->toArray();
            $firstArtist = $chunk->first();
            
            // Get a cover from one of the artist's songs
            $coverSong = Song::whereHas('artists', function($q) use ($chunk) {
                $q->whereIn('artists.id', $chunk->pluck('id'));
            })->whereNotNull('cover')->first();
            
            $this->mixes[] = [
                'id' => $mixNumber,
                'title' => implode(', ', array_slice($artistNames, 0, 2)) . ' and more',
                'image' => $coverSong?->cover_url ?? $firstArtist->photo_url,
                'mix_number' => str_pad($mixNumber, 2, '0', STR_PAD_LEFT),
                'artist_ids' => $chunk->pluck('id')->toArray(),
            ];
            
            $mixNumber++;
        }
    }

    /**
     * Fallback placeholder mixes
     */
    private function getPlaceholderMixes()
    {
        return [
            [
                'id' => 1,
                'title' => 'Your Daily Mix',
                'image' => 'https://picsum.photos/seed/mix1/300/300',
                'mix_number' => '01',
            ],
            [
                'id' => 2,
                'title' => 'Discover Weekly',
                'image' => 'https://picsum.photos/seed/mix2/300/300',
                'mix_number' => '02',
            ],
        ];
    }

    public function render()
    {
        return view('livewire.components.mix-section');
    }
}