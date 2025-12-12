<div class="min-h-screen bg-gradient-to-b from-[#3a506b] to-[#121212]">
    {{-- Header --}}
    <div class="px-6 pt-16 pb-8">
        <div class="flex items-end gap-6">
            {{-- Playlist Cover --}}
            <div class="w-56 h-56 bg-[#282828] rounded-lg shadow-2xl flex items-center justify-center overflow-hidden">
                @if($playlist->cover_image)
                    <img src="{{ $playlist->cover_image }}" class="w-full h-full object-cover" alt="{{ $playlist->name }}">
                @elseif($songs->count() > 0 && $songs->first()->cover_url)
                    <img src="{{ $songs->first()->cover_url }}" class="w-full h-full object-cover opacity-70" alt="{{ $playlist->name }}">
                @else
                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <span class="text-white/70 text-sm font-medium uppercase">Playlist</span>
                
                {{-- Editable Title --}}
                @if($isEditingName && Auth::id() === $playlist->user_id)
                <div x-data x-init="$nextTick(() => $refs.titleInput.focus())">
                    <input 
                        x-ref="titleInput"
                        type="text"
                        wire:model="editingName"
                        wire:keydown.enter="updateName"
                        wire:keydown.escape="cancelEditingName"
                        @blur="$wire.updateName()"
                        class="text-5xl font-black text-white bg-transparent border-b-2 border-green-500 focus:outline-none mt-2 mb-6 w-full"
                    >
                </div>
                @else
                <h1 
                    @if(Auth::id() === $playlist->user_id)
                    wire:click="startEditingName"
                    class="text-7xl font-black text-white mt-2 mb-6 cursor-pointer hover:underline decoration-2 underline-offset-4"
                    title="Click to edit"
                    @else
                    class="text-7xl font-black text-white mt-2 mb-6"
                    @endif
                >
                    {{ $playlist->name }}
                </h1>
                @endif
                
                <p class="text-white/70">
                    <span class="font-medium text-white">{{ $playlist->user->name ?? 'Unknown' }}</span> • {{ count($songs) }} {{ Str::plural('song', count($songs)) }}
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
        <div class="grid grid-cols-[16px_4fr_2fr_1fr] gap-4 px-4 py-2 text-gray-400 text-sm border-b border-white/10 mb-4">
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

            {{-- Album --}}
            <div class="text-gray-400 text-sm truncate">
                {{ $song->album?->title ?? 'Single' }}
            </div>

            {{-- Actions & Duration --}}
            <div class="flex items-center justify-end gap-3">
                {{-- Add to Queue --}}
                <button wire:click.stop="addToQueue({{ $song->id }})"
                    class="text-gray-400 hover:text-white opacity-0 group-hover:opacity-100 transition"
                    title="Add to Queue">
                    <svg class="w-5 h-5 hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                    </svg>
                </button>
                
                {{-- Like Button --}}
                <button wire:click.stop="toggleLike({{ $song->id }})" 
                    class="{{ $this->isLiked($song->id) ? 'text-green-500' : 'text-gray-400 opacity-0 group-hover:opacity-100' }} hover:scale-110 transition">
                    <svg class="w-5 h-5" fill="{{ $this->isLiked($song->id) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>

                {{-- Remove from playlist (only for owner) --}}
                @if(Auth::id() === $playlist->user_id)
                <button wire:click.stop="removeSong({{ $song->id }})"
                    class="text-gray-400 hover:text-red-400 opacity-0 group-hover:opacity-100 transition"
                    title="Remove from playlist">
                    <svg class="w-5 h-5 hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                @endif

                <span class="text-gray-400 text-sm w-12 text-right">{{ $song->duration_formatted }}</span>
            </div>
        </div>
        @endforeach
        @else
        {{-- Empty State --}}
        <div class="text-center py-20">
            <div class="w-20 h-20 mx-auto mb-4 bg-[#282828] rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
            </div>
            <h3 class="text-white text-2xl font-bold mb-2">Let's add some songs</h3>
            <p class="text-gray-400 mb-6">Add songs to this playlist from the song menu.</p>
            <a href="/"
                class="inline-block px-8 py-3 bg-white text-black font-bold rounded-full hover:scale-105 transition">
                Find songs
            </a>
        </div>
        @endif
    </div>
</div>
