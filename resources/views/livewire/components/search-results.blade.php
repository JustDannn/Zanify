<div class="min-h-full">
    <div class="p-6 bg-gradient-to-b from-[#1a1a1a] to-[#121212] min-h-full">
        {{-- Search Input for this page --}}
        <div class="mb-6">
            <div class="max-w-xl">
                <div class="flex items-center bg-[#282828] rounded-full px-4 py-3">
                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="query" type="text" placeholder="What do you want to play?"
                        class="bg-transparent w-full focus:outline-none text-white text-lg" autofocus />
                    @if($query)
                    <button wire:click="$set('query', '')" class="text-gray-400 hover:text-white transition ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        @if($isSearching)
        {{-- Search Results Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Search results for "{{ $query }}"</h1>
        </div>

        @if($topResult || $songs->count() > 0 || $albums->count() > 0 || $artists->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-[400px_1fr] gap-6">

            {{-- TOP RESULT --}}
            @if($topResult)
            <div>
                <h2 class="text-xl font-bold text-white mb-4">Top result</h2>

                @if($topResult['type'] === 'song')
                {{-- Song Top Result --}}
                <div class="bg-[#181818] hover:bg-[#282828] rounded-lg p-5 transition-all duration-300 group cursor-pointer relative"
                    wire:click="playSong({{ $topResult['item']->id }})">
                    <img src="{{ $topResult['item']->cover_url }}" alt="{{ $topResult['item']->title }}"
                        class="w-24 h-24 rounded-lg shadow-lg mb-4 object-cover">
                    <h3 class="text-3xl font-bold text-white mb-2 truncate">{{ $topResult['item']->title }}</h3>
                    <p class="text-gray-400">
                        <span
                            class="bg-[#121212] text-white text-xs font-medium px-2 py-1 rounded-full mr-2">Song</span>
                        {{ $topResult['item']->artist_display }}
                    </p>
                    <button wire:click.stop="playSong({{ $topResult['item']->id }})"
                        class="absolute bottom-5 right-5 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 hover:bg-green-400">
                        <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                </div>

                @elseif($topResult['type'] === 'album')
                {{-- Album Top Result --}}
                <a href="{{ route('album', $topResult['item']->id) }}" wire:navigate
                    class="block bg-[#181818] hover:bg-[#282828] rounded-lg p-5 transition-all duration-300 group cursor-pointer relative">
                    <img src="{{ $topResult['item']->cover_url }}" alt="{{ $topResult['item']->title }}"
                        class="w-24 h-24 rounded-lg shadow-lg mb-4 object-cover">
                    <h3 class="text-3xl font-bold text-white mb-2 truncate">{{ $topResult['item']->title }}</h3>
                    <p class="text-gray-400">
                        <span
                            class="bg-[#121212] text-white text-xs font-medium px-2 py-1 rounded-full mr-2">Album</span>
                        {{ $topResult['item']->artist_name }}
                    </p>
                    <button wire:click.prevent="playAlbum({{ $topResult['item']->id }})"
                        class="absolute bottom-5 right-5 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 hover:bg-green-400">
                        <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                </a>

                @elseif($topResult['type'] === 'artist')
                {{-- Artist Top Result --}}
                <a href="{{ route('artist', $topResult['item']->id) }}" wire:navigate
                    class="block bg-[#181818] hover:bg-[#282828] rounded-lg p-5 transition-all duration-300 group cursor-pointer relative">
                    <img src="{{ $topResult['item']->photo_url ?? 'https://api.dicebear.com/9.x/shapes/svg?seed=' . urlencode($topResult['item']->name) }}"
                        alt="{{ $topResult['item']->name }}" class="w-24 h-24 rounded-full shadow-lg mb-4 object-cover">
                    <h3 class="text-3xl font-bold text-white mb-2 truncate">{{ $topResult['item']->name }}</h3>
                    <p class="text-gray-400">
                        <span
                            class="bg-[#121212] text-white text-xs font-medium px-2 py-1 rounded-full mr-2">Artist</span>
                    </p>
                    <button wire:click.prevent="playArtist({{ $topResult['item']->id }})"
                        class="absolute bottom-5 right-5 w-12 h-12 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 hover:bg-green-400">
                        <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                </a>
                @endif
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
                            <svg class="w-5 h-5 text-green-500 hover:scale-110 transition" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                            </svg>
                            @else
                            <svg class="w-5 h-5 text-gray-400 hover:text-white hover:scale-110 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            @endif
                        </button>

                        {{-- Add to Playlist --}}
                        @include('livewire.partials.playlist-menu', ['songId' => $song->id])

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

        {{-- ARTISTS SECTION --}}
        @if($artists->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-white mb-4">Artists</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($artists as $artist)
                <a href="{{ route('artist', $artist->id) }}" wire:navigate
                    class="bg-[#181818] hover:bg-[#282828] p-4 rounded-lg transition-all duration-300 group cursor-pointer">
                    <div class="relative mb-4">
                        <img src="{{ $artist->photo_url ?? 'https://api.dicebear.com/9.x/shapes/svg?seed=' . urlencode($artist->name) }}"
                            alt="{{ $artist->name }}" class="w-full aspect-square rounded-full object-cover shadow-lg">
                        <button wire:click.prevent="playArtist({{ $artist->id }})"
                            class="absolute bottom-2 right-2 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-white font-semibold truncate text-center">{{ $artist->name }}</h3>
                    <p class="text-gray-400 text-sm truncate text-center">Artist</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ALBUMS SECTION --}}
        @if($albums->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold text-white mb-4">Albums</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($albums as $album)
                <a href="{{ route('album', $album->id) }}" wire:navigate
                    class="bg-[#181818] hover:bg-[#282828] p-4 rounded-lg transition-all duration-300 group cursor-pointer">
                    <div class="relative mb-4">
                        <img src="{{ $album->cover_url }}" alt="{{ $album->title }}"
                            class="w-full aspect-square rounded-md object-cover shadow-lg">
                        <button wire:click.prevent="playAlbum({{ $album->id }})"
                            class="absolute bottom-2 right-2 w-10 h-10 bg-green-500 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-xl hover:scale-105 translate-y-2 group-hover:translate-y-0">
                            <svg class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-white font-semibold truncate">{{ $album->title }}</h3>
                    <p class="text-gray-400 text-sm truncate">{{ $album->year }} • {{ $album->artist_name }}</p>
                </a>
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

        @else
        {{-- Empty State - No search yet --}}
        <div class="flex flex-col items-center justify-center py-20">
            <svg class="w-20 h-20 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-2xl font-bold text-white mb-2">Search Zanify</h3>
            <p class="text-gray-400">Find your favorite songs, albums, and artists</p>
        </div>
        @endif
    </div>
</div>