{{-- Queue Sidebar --}}
<div>
    {{-- Overlay --}}
    @if($isOpen)
    <div class="fixed inset-0 bg-black/50 z-40" wire:click="toggleQueue"></div>
    @endif

    {{-- Sidebar --}}
    <div
        class="fixed top-0 right-0 h-full w-80 bg-[#121212] border-l border-[#282828] z-50 transform transition-transform duration-300 ease-in-out {{ $isOpen ? 'translate-x-0' : 'translate-x-full' }}">
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-4 border-b border-[#282828]">
            <h2 class="text-white font-bold text-lg">Queue</h2>
            <div class="flex items-center gap-2">
                @if(count($queue) > 0)
                <button wire:click="clearQueue" class="text-gray-400 hover:text-white text-sm transition">
                    Clear
                </button>
                @endif
                <button wire:click="toggleQueue" class="text-gray-400 hover:text-white transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Now Playing --}}
        @if($currentSongId)
        <div class="px-4 py-3 border-b border-[#282828]">
            <p class="text-gray-400 text-xs uppercase tracking-wider mb-2">Now Playing</p>
            <div class="flex items-center gap-3 p-2 bg-white/5 rounded-lg">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-white text-sm">Current song</span>
            </div>
        </div>
        @endif

        {{-- Queue List --}}
        <div class="px-4 py-3 flex-1 overflow-y-auto" style="max-height: calc(100vh - 200px);">
            <p class="text-gray-400 text-xs uppercase tracking-wider mb-3">Next Up • {{ count($queue) }} {{
                Str::plural('song', count($queue)) }}</p>

            @if(count($queue) > 0)
            <div class="space-y-1">
                @foreach($queue as $index => $song)
                <div class="group flex items-center gap-3 p-2 rounded-lg hover:bg-white/10 transition cursor-pointer"
                    wire:key="queue-{{ $song['id'] }}-{{ $index }}">
                    {{-- Index / Play Button --}}
                    <div class="w-5 text-center">
                        <span class="text-gray-400 text-sm group-hover:hidden">{{ $index + 1 }}</span>
                        <button wire:click="playFromQueue({{ $index }})" class="hidden group-hover:block text-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Song Info --}}
                    <img src="{{ $song['cover'] }}" class="w-10 h-10 rounded object-cover" alt="{{ $song['title'] }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ $song['title'] }}</p>
                        <p class="text-gray-400 text-xs truncate">{{ $song['artist'] }}</p>
                    </div>

                    {{-- Duration --}}
                    <span class="text-gray-400 text-xs">{{ $song['duration_formatted'] }}</span>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                        @if($index > 0)
                        <button wire:click="moveUp({{ $index }})" class="text-gray-400 hover:text-white p-1"
                            title="Move Up">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        @endif
                        @if($index < count($queue) - 1) <button wire:click="moveDown({{ $index }})"
                            class="text-gray-400 hover:text-white p-1" title="Move Down">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                            </button>
                            @endif
                            <button wire:click="removeFromQueue({{ $index }})"
                                class="text-gray-400 hover:text-red-500 p-1" title="Remove">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-16 h-16 bg-[#282828] rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-1">Queue is empty</h3>
                <p class="text-gray-400 text-sm">Add songs to your queue to see them here</p>
            </div>
            @endif
        </div>
    </div>
</div>