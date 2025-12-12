<div class="w-64 bg-[#0f0f0f] text-gray-200 h-screen flex flex-col">

    {{-- TITLE --}}
    <div class="px-4 py-4 flex items-center justify-between">
        <h2 class="font-semibold text-lg">Your Library</h2>
        <button wire:click="startCreatingPlaylist" class="p-1 hover:bg-gray-800 rounded transition"
            title="Create playlist">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
            </svg>
        </button>
    </div>

    {{-- FILTER BUTTONS --}}
    <div class="flex gap-2 px-4 pb-2">
        <button class="px-3 py-1 bg-[#1f1f1f] rounded-full text-sm hover:bg-[#282828] transition">Playlists</button>
        <button class="px-3 py-1 bg-[#1f1f1f] rounded-full text-sm hover:bg-[#282828] transition">Artists</button>
    </div>

    {{-- SEARCH + SORT --}}
    <div class="px-4 pb-2 flex items-center justify-between">
        <input type="text" placeholder="Search"
            class="w-full bg-[#1c1c1c] text-sm px-3 py-1 rounded-full focus:outline-none">
        <button class="ml-2 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18m-7 6h7" />
            </svg>
        </button>
    </div>

    {{-- LIST --}}
    <div class="overflow-y-auto scrollbar-hide flex-1 px-2">

        {{-- NEW PLAYLIST INPUT --}}
        @if($isCreatingPlaylist)
        <div x-data="{ focused: true }" bg-linear-to-br x-init="$nextTick(() => $refs.newPlaylistInput.focus())"
            class="flex items-center gap-3 px-2 py-2 rounded bg-[#1a1a1a]">
            {{-- Playlist Icon --}}
            <div class="w-12 h-12 rounded bg-[#282828] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <input x-ref="newPlaylistInput" type="text" wire:model="newPlaylistName"
                    wire:keydown.enter="createPlaylist" wire:keydown.escape="cancelCreatingPlaylist"
                    @blur="$wire.createPlaylist()" placeholder="My Playlist"
                    class="w-full bg-transparent text-white font-medium focus:outline-none border-b border-green-500 pb-1">
                <div class="text-sm text-gray-400 mt-1">Press Enter to save</div>
            </div>
        </div>
        @endif

        @foreach ($items as $item)
        {{-- Check if editing this playlist --}}
        @if(!empty($item['id']) && $editingPlaylistId === $item['id'])
        <div x-data x-init="$nextTick(() => $refs.editInput.focus())"
            class="flex items-center gap-3 px-2 py-2 rounded bg-[#1a1a1a]">
            <img src="{{ $item['image'] ?? 'https://api.dicebear.com/9.x/shapes/svg?seed=' . urlencode($item['title']) }}"
                class="w-12 h-12 rounded object-cover shrink-0">

            <div class="min-w-0 flex-1">
                <input x-ref="editInput" type="text" wire:model="editingPlaylistName"
                    wire:keydown.enter="updatePlaylistName" wire:keydown.escape="cancelEditingPlaylist"
                    @blur="$wire.updatePlaylistName()"
                    class="w-full bg-transparent text-white font-medium focus:outline-none border-b border-green-500 pb-1">
                <div class="text-sm text-gray-400 truncate">{{ $item['subtitle'] }}</div>
            </div>
        </div>
        @else
        <a href="{{ $item['route'] ?? '#' }}" wire:navigate @if(!empty($item['id'])) x-data
            @contextmenu.prevent="$wire.startEditingPlaylist({{ $item['id'] }})" @endif
            class="flex items-center gap-3 px-2 py-2 rounded hover:bg-[#1a1a1a] cursor-pointer transition group">

            {{-- IMAGE / SPECIAL LIKED SONGS GRADIENT --}}
            @if(!empty($item['is_liked_songs']))
            <div
                class="w-12 h-12 rounded bg-linear-to-br from-[#450af5] to-[#c4efd9] flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
            </div>
            @else
            <img src="{{ $item['image'] ?? 'https://api.dicebear.com/9.x/shapes/svg?seed=' . urlencode($item['title']) }}"
                class="w-12 h-12 rounded object-cover shrink-0 {{ $item['type'] === 'artist' ? 'rounded-full' : '' }}">
            @endif

            <div class="min-w-0 flex-1">
                <div class="font-medium truncate {{ !empty($item['is_liked_songs']) ? 'text-white' : '' }}">{{
                    $item['title'] }}</div>
                <div class="text-sm text-gray-400 truncate">{{ $item['subtitle'] }}</div>
            </div>

            {{-- Delete button for playlists (not liked songs) --}}
            @if(!empty($item['id']) && empty($item['is_liked_songs']))
            <button wire:click.prevent="deletePlaylist({{ $item['id'] }})"
                class="opacity-0 group-hover:opacity-100 p-1 hover:bg-red-500/20 rounded transition"
                title="Delete playlist">
                <svg class="w-4 h-4 text-gray-400 hover:text-red-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            @endif

        </a>
        @endif
        @endforeach

    </div>

</div>