{{-- Add to Playlist Dropdown Menu --}}
{{-- Usage: @include('livewire.partials.playlist-menu', ['songId' => $song->id]) --}}

<div class="relative" 
    x-data="{ 
        get isOpen() { return $wire.showPlaylistMenuForSong === {{ $songId }} }
    }" 
    @click.away="$wire.closePlaylistMenu()"
>
    {{-- Add to Playlist Button --}}
    <button 
        wire:click.stop="togglePlaylistMenu({{ $songId }})"
        class="text-gray-400 hover:text-white opacity-0 group-hover:opacity-100 transition"
        title="Add to Playlist"
    >
        <svg class="w-5 h-5 hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
    </button>

    {{-- Dropdown Menu --}}
    @if($showPlaylistMenuForSong === $songId)
    <div 
        class="fixed w-56 bg-[#282828] rounded-lg shadow-2xl py-2 border border-white/10"
        style="z-index: 9999;"
        x-init="
            $nextTick(() => {
                const btn = $el.previousElementSibling;
                const rect = btn.getBoundingClientRect();
                const menuHeight = $el.offsetHeight;
                const spaceAbove = rect.top;
                const spaceBelow = window.innerHeight - rect.bottom;
                
                // Position horizontally
                let left = rect.left - 200;
                if (left < 10) left = 10;
                if (left + 224 > window.innerWidth) left = window.innerWidth - 234;
                $el.style.left = left + 'px';
                
                // Position vertically - prefer above, fallback to below
                if (spaceAbove > menuHeight + 10) {
                    $el.style.top = (rect.top - menuHeight - 8) + 'px';
                } else {
                    $el.style.top = (rect.bottom + 8) + 'px';
                }
            })
        "
        wire:click.stop
    >
        <div class="px-3 py-2 border-b border-white/10">
            <p class="text-white text-sm font-medium">Add to playlist</p>
        </div>
        
        {{-- Create New Playlist --}}
        <button 
            wire:click="createPlaylistWithSong({{ $songId }})"
            class="w-full px-3 py-2 text-left text-sm text-gray-300 hover:bg-white/10 hover:text-white transition flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create new playlist
        </button>

        @if(count($userPlaylists) > 0)
        <div class="border-t border-white/10 mt-1 pt-1 max-h-48 overflow-y-auto">
            @foreach($userPlaylists as $playlist)
            <button 
                wire:click="addToPlaylist({{ $playlist['id'] }}, {{ $songId }})"
                class="w-full px-3 py-2 text-left text-sm text-gray-300 hover:bg-white/10 hover:text-white transition flex items-center gap-2"
            >
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                </svg>
                <span class="truncate">{{ $playlist['name'] }}</span>
            </button>
            @endforeach
        </div>
        @endif
    </div>
    @endif
</div>
