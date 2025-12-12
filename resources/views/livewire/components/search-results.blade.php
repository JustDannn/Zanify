<div x-data x-init="$nextTick(() => $dispatch('search-active', { active: {{ $isSearching ? 'true' : 'false' }} }))"
    x-effect="$dispatch('search-active', { active: {{ $isSearching ? 'true' : 'false' }} }}" class="min-h-full">
    @if($isSearching)
    <div class="p-6 bg-linear-to-b from-[#1a1a1a] to-[#121212] min-h-full">
        {{-- Search Results Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Search results for "{{ $query }}"</h1>
        </div>

        @if($topResult || $songs->count() > 0 || $albums->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-[400px_1fr] gap-6">

            {{-- TOP RESULT --}}
            @if($topResult)
            <div>
                <h2 class="text-xl font-bold text-white mb-4">Top result</h2>
                <div class="bg-[#181818] hover:bg-[#282828] rounded-lg p-5 transition-all duration-300 group cursor-pointer relative"
                    @if($topResult['type']==='song' ) wire:click="playSong({{ $topResult['item']->id }})" @endif>
                    @if($topResult['type'] === 'song')
                    <img src="{{ $topResult['item']->cover_url }}" alt="{{ $topResult['item']->title }}"
                        class="w-24 h-24 rounded-lg shadow-lg mb-4 object-cover">
                    <h3 class="text-3xl font-bold text-white mb-2 truncate">{{ $topResult['item']->title }}</h3>
                    <p class="text-gray-400">
                        <span
                            class="bg-[#121212] text-white text-xs font-medium px-2 py-1 rounded-full mr-2">Song</span>
                        {{ $topResult['item']->artist_display }}
                    </p>
                    @else
                    <img src="{{ $topResult['item']->cover_url }}" alt="{{ $topResult['item']->title }}"
                        class="w-24 h-24 rounded-lg shadow-lg mb-4 object-cover">
                    <h3 class="text-3xl font-bold text-white mb-2 truncate">{{ $topResult['item']->title }}</h3>
                    <p class="text-gray-400">
                        <span
                            class="bg-[#121212] text-white text-xs font-medium px-2 py-1 rounded-full mr-2">Album</span>
                        {{ $topResult['item']->artist_name }}
                    </p>
                    @endif

                    {{-- Play Button --}}
                    <button @if($topResult['type']==='song' ) wire:click.stop="playSong({{ $topResult['item']->id }})"
                        @endif
                        class="absolute bottom-5 right-5 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 hover:bg-green-400">
                        <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                </div>
            </div>
            @endif

            {{-- SONGS LIST --}}
            @if($songs->count() > 0)
            <div>
                <h2 class="text-xl font-bold text-white mb-4">Songs</h2>
                <div class="space-y-2">
                    @foreach($songs as $song)
                    <div class="flex items-center gap-4 p-2 rounded-md hover:bg-[#282828] transition group cursor-pointer"
                        wire:click="playSong({{ $song->id }})">
                        {{-- Cover --}}
                        <div class="relative">
                            <img src="{{ $song->cover_url }}" alt="{{ $song->title }}"
                                class="w-10 h-10 rounded object-cover">
                            <button wire:click.stop="playSong({{ $song->id }})"
                                class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                        </div>

                        {{-- Song Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium truncate">{{ $song->title }}</p>
                            <p class="text-gray-400 text-sm truncate">{{ $song->artist_display }}</p>
                        </div>

                        {{-- Like Button --}}
                        <button wire:click.stop="toggleLike({{ $song->id }})"
                            class="opacity-0 group-hover:opacity-100 transition {{ in_array($song->id, $likedSongIds) ? 'opacity-100' : '' }}">
                            @if(in_array($song->id, $likedSongIds))
                            {{-- Liked (filled green heart) --}}
                            <svg class="w-5 h-5 text-green-500 hover:scale-110 transition" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            @else
                            {{-- Not liked (outline heart) --}}
                            <svg class="w-5 h-5 text-gray-400 hover:text-white hover:scale-110 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            @endif
                        </button>

                        {{-- Add to Queue Button --}}
                        <button wire:click.stop="addToQueue({{ $song->id }})"
                            class="opacity-0 group-hover:opacity-100 transition text-gray-400 hover:text-white"
                            title="Add to Queue">
                            <svg class="w-5 h-5 hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                            </svg>
                        </button>

                        {{-- Duration --}}
                        <span class="text-gray-400 text-sm w-12 text-right">{{ $song->duration_formatted }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ALBUMS SECTION --}}
        @if($albums->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-white mb-4">Albums</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($albums as $album)
                <div
                    class="bg-[#181818] hover:bg-[#282828] p-4 rounded-lg transition-all duration-300 group cursor-pointer">
                    <div class="relative mb-4">
                        <img src="{{ $album->cover_url }}" alt="{{ $album->title }}"
                            class="w-full aspect-square rounded-md object-cover shadow-lg">
                        <button
                            class="absolute bottom-2 right-2 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-white font-semibold truncate">{{ $album->title }}</h3>
                    <p class="text-gray-400 text-sm truncate">{{ $album->year }} • {{ $album->artist_name }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @else
        {{-- No Results --}}
        <div class="flex flex-col items-center justify-center py-20">
            <svg class="w-16 h-16 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-xl font-bold text-white mb-2">No results found for "{{ $query }}"</h3>
            <p class="text-gray-400">Please make sure your words are spelled correctly, or use fewer or different
                keywords.</p>
        </div>
        @endif
    </div>
    @endif
</div>