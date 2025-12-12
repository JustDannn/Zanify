<div class="min-h-screen bg-linear-to-b from-[#3d3d3d] to-[#121212]">
    @if($album)
    {{-- Header --}}
    <div class="px-6 pt-16 pb-8">
        <div class="flex items-end gap-6">
            {{-- Album Cover --}}
            <div class="w-56 h-56 rounded-lg shadow-2xl overflow-hidden shrink-0">
                <img src="{{ $album->cover_url }}" alt="{{ $album->title }}" class="w-full h-full object-cover">
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <span class="text-white/70 text-sm font-medium uppercase">Album</span>
                <h1 class="text-5xl md:text-7xl font-black text-white mt-2 mb-4 line-clamp-2">{{ $album->title }}</h1>
                <div class="flex items-center gap-2 text-white/70">
                    @if($album->artist)
                    <a href="{{ route('artist', $album->artist->id) }}" wire:navigate
                        class="font-semibold text-white hover:underline">
                        {{ $album->artist->name }}
                    </a>
                    <span>•</span>
                    @endif
                    @if($album->year)
                    <span>{{ $album->year }}</span>
                    <span>•</span>
                    @endif
                    <span>{{ count($songs) }} {{ Str::plural('song', count($songs)) }}</span>
                    <span>•</span>
                    <span>{{ $this->formatTotalDuration() }}</span>
                </div>
                <div class="mt-2 text-gray-400 text-sm">
                    <span>{{ number_format($totalPlays) }} total plays</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    @if(count($songs) > 0)
    <div class="px-6 py-4 flex items-center gap-6">
        {{-- Play Button --}}
        <button wire:click="playAlbum"
            class="w-14 h-14 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center transition-all shadow-xl">
            <svg class="w-6 h-6 text-black ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>
        </button>

        {{-- Shuffle --}}
        <button class="text-gray-400 hover:text-white transition">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z" />
            </svg>
        </button>
    </div>
    @endif

    {{-- Songs List --}}
    <div class="px-6 pb-32">
        @if(count($songs) > 0)
        {{-- Table Header --}}
        <div
            class="grid grid-cols-[16px_4fr_2fr_1fr] gap-4 px-4 py-2 text-gray-400 text-sm border-b border-white/10 mb-4">
            <span>#</span>
            <span>Title</span>
            <span>Plays</span>
            <span class="flex justify-end">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>

        {{-- Songs --}}
        @foreach($songs as $index => $song)
        <div class="group grid grid-cols-[16px_4fr_2fr_1fr] gap-4 px-4 py-2 rounded-md hover:bg-white/10 transition items-center cursor-pointer"
            wire:click="playSong({{ $song->id }})">
            {{-- Number / Play --}}
            <div class="text-gray-400 group-hover:hidden">{{ $index + 1 }}</div>
            <button wire:click.stop="playSong({{ $song->id }})" class="hidden group-hover:block text-white">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z" />
                </svg>
            </button>

            {{-- Title & Artist --}}
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ $song->cover_url }}" class="w-10 h-10 rounded object-cover" alt="{{ $song->title }}">
                <div class="min-w-0">
                    <p class="text-white font-medium truncate">{{ $song->title }}</p>
                    <p class="text-gray-400 text-sm truncate">{{ $song->artist_display }}</p>
                </div>
            </div>

            {{-- Play Count --}}
            <div class="text-gray-400 text-sm">
                {{ number_format($song->play_count) }}
            </div>

            {{-- Actions & Duration --}}
            <div class="flex items-center justify-end gap-3">
                {{-- Add to Queue --}}
                <button wire:click.stop="addToQueue({{ $song->id }})"
                    class="text-gray-400 hover:text-white opacity-0 group-hover:opacity-100 transition"
                    title="Add to Queue">
                    <svg class="w-5 h-5 hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                    </svg>
                </button>

                {{-- Add to Playlist --}}
                @include('livewire.partials.playlist-menu', ['songId' => $song->id])

                {{-- Like --}}
                <button wire:click.stop="toggleLike({{ $song->id }})"
                    class="{{ $this->isLiked($song->id) ? 'text-green-500' : 'text-gray-400 opacity-0 group-hover:opacity-100' }} hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                </button>

                {{-- Duration --}}
                <span class="text-gray-400 text-sm w-12 text-right">{{ $song->duration_formatted }}</span>
            </div>
        </div>
        @endforeach
        @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-[#282828] rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
                </svg>
            </div>
            <h3 class="text-white text-xl font-bold mb-2">No songs in this album</h3>
            <p class="text-gray-400">This album doesn't have any songs yet.</p>
        </div>
        @endif
    </div>
    @else
    {{-- Album not found --}}
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h2 class="text-white text-2xl font-bold mb-2">Album not found</h2>
            <a href="{{ route('home') }}" wire:navigate class="text-green-500 hover:underline">Go back home</a>
        </div>
    </div>
    @endif
</div>