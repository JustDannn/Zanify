<div class="min-h-screen bg-[#121212] overflow-x-hidden scrollbar-hide">
    @if($artist)
    {{-- Header with Artist Photo --}}
    <div class="relative min-h-[350px]">
        {{-- Background Gradient - Seamless --}}
        <div class="absolute inset-0 bg-linear-to-b from-[#4a4a4a] via-[#2d2d2d] via-60% to-[#121212]"></div>

        {{-- Content --}}
        <div class="relative px-6 pt-20 pb-6">
            <div class="flex items-end gap-6">
                {{-- Artist Photo --}}
                <div class="w-48 h-48 rounded-full shadow-2xl overflow-hidden shrink-0 ring-4 ring-black/20">
                    <img src="{{ $artist->photo_url }}" alt="{{ $artist->name }}" class="w-full h-full object-cover">
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                        </svg>
                        <span class="text-white text-sm font-medium">Verified Artist</span>
                    </div>
                    <h1 class="text-6xl md:text-8xl font-black text-white mb-4">{{ $artist->name }}</h1>
                    <p class="text-white/70 text-lg">
                        {{ $this->formatListeners() }} monthly listeners
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Bar - Always show --}}
    <div class="px-6 py-4 flex items-center gap-6 bg-linear-to-b from-[#121212]/0 to-[#121212]">
        {{-- Play Button --}}
        <button wire:click="playArtist"
            class="w-14 h-14 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center transition-all shadow-xl">
            <svg class="w-6 h-6 text-black ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>
        </button>

        {{-- Follow Button --}}
        <button
            class="px-8 py-2 border border-gray-400 text-white font-bold rounded-full hover:border-white hover:scale-105 transition">
            Follow
        </button>
    </div>

    {{-- Popular Songs --}}
    <div class="px-6 pb-8">
        <h2 class="text-2xl font-bold text-white mb-4">Popular</h2>

        @if(count($popularSongs) > 0)
        <div class="space-y-2">
            @foreach($popularSongs as $index => $song)
            <div class="group flex items-center gap-4 px-4 py-2 rounded-md hover:bg-white/10 transition cursor-pointer"
                wire:click="playSong({{ $song->id }})">
                {{-- Number / Play --}}
                <div class="w-6 text-center">
                    <span class="text-gray-400 group-hover:hidden">{{ $index + 1 }}</span>
                    <button wire:click.stop="playSong({{ $song->id }})" class="hidden group-hover:block text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                </div>

                {{-- Song Info --}}
                <img src="{{ $song->cover_url }}" class="w-10 h-10 rounded object-cover" alt="{{ $song->title }}">
                <div class="flex-1 min-w-0">
                    <p class="text-white font-medium truncate">{{ $song->title }}</p>
                </div>

                {{-- Play Count --}}
                <div class="text-gray-400 text-sm w-24 text-right">
                    {{ number_format($song->play_count) }}
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3">
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

                    {{-- Add to Playlist --}}
                    @include('livewire.partials.playlist-menu', ['songId' => $song->id])

                    {{-- Add to Queue --}}
                    <button wire:click.stop="addToQueue({{ $song->id }})"
                        class="text-gray-400 hover:text-white opacity-0 group-hover:opacity-100 transition"
                        title="Add to Queue">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-400">No songs available yet.</p>
        @endif
    </div>

    {{-- Discography (Albums) --}}
    @if(count($albums) > 0)
    <div class="px-6 pb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-white">Discography</h2>
            @if(count($albums) > 4)
            <a href="#" class="text-sm font-bold text-gray-400 hover:underline hover:text-white">Show all</a>
            @endif
        </div>

        <div class="flex space-x-4 pb-4 overflow-x-auto scrollbar-hide">
            @foreach($albums as $album)
            <a href="{{ route('album', $album->id) }}" wire:navigate
                class="shrink-0 w-[180px] group cursor-pointer p-4 bg-white/5 hover:bg-white/10 rounded-lg transition">
                {{-- Album Cover --}}
                <div class="relative mb-3">
                    <div class="rounded-lg overflow-hidden aspect-square bg-[#282828] shadow-lg">
                        <img src="{{ $album->cover_url }}" alt="{{ $album->title }}" class="w-full h-full object-cover">
                    </div>

                    {{-- Play Button --}}
                    <div
                        class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                        <button
                            class="w-12 h-12 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center shadow-xl transition">
                            <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Album Info --}}
                <p class="text-white font-semibold text-sm truncate">{{ $album->title }}</p>
                <p class="text-gray-400 text-sm">
                    {{ $album->year ?? 'Unknown' }} • Album
                </p>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Stats Section --}}
    <div class="px-6 pb-32">
        <h2 class="text-2xl font-bold text-white mb-4">Stats</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/5 rounded-lg p-4">
                <p class="text-gray-400 text-sm">Total Plays</p>
                <p class="text-white text-2xl font-bold">{{ number_format($totalPlays) }}</p>
            </div>
            <div class="bg-white/5 rounded-lg p-4">
                <p class="text-gray-400 text-sm">Listeners</p>
                <p class="text-white text-2xl font-bold">{{ number_format($totalListeners) }}</p>
            </div>
            <div class="bg-white/5 rounded-lg p-4">
                <p class="text-gray-400 text-sm">Songs</p>
                <p class="text-white text-2xl font-bold">{{ $artist->songs()->count() }}</p>
            </div>
            <div class="bg-white/5 rounded-lg p-4">
                <p class="text-gray-400 text-sm">Albums</p>
                <p class="text-white text-2xl font-bold">{{ count($albums) }}</p>
            </div>
        </div>
    </div>
    @else
    {{-- Artist not found --}}
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h2 class="text-white text-2xl font-bold mb-2">Artist not found</h2>
            <a href="{{ route('home') }}" wire:navigate class="text-green-500 hover:underline">Go back home</a>
        </div>
    </div>
    @endif
</div>