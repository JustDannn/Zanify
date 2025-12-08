<div class="min-h-screen bg-gradient-to-b from-[#5038a0] to-[#121212]">
    {{-- Header --}}
    <div class="px-6 pt-16 pb-8">
        <div class="flex items-end gap-6">
            {{-- Liked Songs Cover --}}
            <div
                class="w-56 h-56 bg-gradient-to-br from-[#450af5] to-[#c4efd9] rounded-lg shadow-2xl flex items-center justify-center">
                <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <span class="text-white/70 text-sm font-medium uppercase">Playlist</span>
                <h1 class="text-7xl font-black text-white mt-2 mb-6">Liked Songs</h1>
                <p class="text-white/70">
                    @auth
                    <span class="font-medium text-white">{{ Auth::user()->name }}</span> • {{ count($songs) }} {{
                    Str::plural('song', count($songs)) }}
                    @else
                    <a href="{{ route('login') }}" class="text-green-400 hover:underline">Login</a> to see your liked
                    songs
                    @endauth
                </p>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    @if(count($songs) > 0)
    <div class="px-6 py-4 flex items-center gap-6">
        {{-- Play Button --}}
        <button wire:click="playSong({{ $songs->first()->id }})"
            class="w-14 h-14 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center transition-all shadow-xl">
            <svg class="w-6 h-6 text-black ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
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
            <span>Album</span>
            <span class="flex justify-end">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
        </div>

        {{-- Songs --}}
        @foreach($songs as $index => $song)
        <div class="group grid grid-cols-[16px_4fr_2fr_1fr] gap-4 px-4 py-2 rounded-md hover:bg-white/10 transition items-center"
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

            {{-- Album --}}
            <div class="text-gray-400 text-sm truncate">
                {{ $song->album?->title ?? 'Single' }}
            </div>

            {{-- Like Button, Queue & Duration --}}
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
                {{-- Unlike --}}
                <button wire:click.stop="toggleLike({{ $song->id }})" class="text-green-500 hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                </button>
                <span class="text-gray-400 text-sm w-12 text-right">{{ $song->duration_formatted }}</span>
            </div>
        </div>
        @endforeach
        @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="w-20 h-20 mx-auto mb-4 bg-[#282828] rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h3 class="text-white text-2xl font-bold mb-2">Songs you like will appear here</h3>
            <p class="text-gray-400 mb-6">Save songs by tapping the heart icon.</p>
            <a href="/"
                class="inline-block px-8 py-3 bg-white text-black font-bold rounded-full hover:scale-105 transition">
                Find songs
            </a>
        </div>
        @endif
    </div>
</div>