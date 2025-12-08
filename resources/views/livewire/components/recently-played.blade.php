<div class="min-h-screen bg-gradient-to-b from-[#1a1a2e] to-[#121212]">
    {{-- Header --}}
    <div class="px-6 pt-16 pb-8">
        <div class="flex items-end gap-6">
            {{-- History Icon --}}
            <div
                class="w-56 h-56 bg-gradient-to-br from-[#2d6a4f] to-[#40916c] rounded-lg shadow-2xl flex items-center justify-center">
                <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            {{-- Info --}}
            <div class="flex-1">
                <span class="text-white/70 text-sm font-medium uppercase">History</span>
                <h1 class="text-7xl font-black text-white mt-2 mb-6">Recently Played</h1>
                <p class="text-white/70">
                    @auth
                    <span class="font-medium text-white">{{ Auth::user()->name }}</span> • {{ $totalCount }}
                    {{ Str::plural('song', $totalCount) }} dalam 7 hari terakhir
                    @endauth
                </p>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    @if($totalCount > 0)
    <div class="px-6 py-4 flex items-center gap-6">
        {{-- Play All Button --}}
        @if(count($groupedSongs) > 0 && count($groupedSongs[0]['songs']) > 0)
        <button wire:click="playSong({{ $groupedSongs[0]['songs'][0]['id'] }})"
            class="w-14 h-14 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center transition-all shadow-xl">
            <svg class="w-6 h-6 text-black ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
            </svg>
        </button>
        @endif

        {{-- Clear History --}}
        <button wire:click="clearHistory" wire:confirm="Yakin mau hapus semua history?"
            class="px-4 py-2 text-gray-400 hover:text-white border border-gray-600 hover:border-white rounded-full transition">
            Hapus History
        </button>
    </div>
    @endif

    {{-- Songs List Grouped by Date --}}
    <div class="px-6 pb-32">
        @if($totalCount > 0)
        @foreach($groupedSongs as $group)
        <div class="mb-8">
            {{-- Date Header --}}
            <div class="flex items-center gap-4 mb-4">
                <h2 class="text-xl font-bold text-white">{{ $group['date'] }}</h2>
                <div class="flex-1 h-px bg-white/10"></div>
                <span class="text-sm text-gray-400">{{ count($group['songs']) }} {{ Str::plural('lagu',
                    count($group['songs'])) }}</span>
            </div>

            {{-- Table Header --}}
            <div
                class="grid grid-cols-[16px_4fr_2fr_minmax(60px,1fr)_80px] gap-4 px-4 py-2 text-gray-400 text-sm border-b border-white/10 mb-2">
                <span>#</span>
                <span>Title</span>
                <span>Album</span>
                <span>Played</span>
                <span class="flex justify-end">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>

            {{-- Songs --}}
            @foreach($group['songs'] as $index => $song)
            <div class="group grid grid-cols-[16px_4fr_2fr_minmax(60px,1fr)_80px] gap-4 px-4 py-2 rounded-md hover:bg-white/10 transition items-center cursor-pointer"
                wire:click="playSong({{ $song['id'] }})">
                {{-- Number / Play --}}
                <div class="text-gray-400 group-hover:hidden">{{ $index + 1 }}</div>
                <button wire:click.stop="playSong({{ $song['id'] }})" class="hidden group-hover:block text-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                </button>

                {{-- Title & Artist --}}
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ $song['cover'] }}" class="w-10 h-10 rounded object-cover" alt="{{ $song['title'] }}">
                    <div class="min-w-0">
                        <p class="text-white font-medium truncate">{{ $song['title'] }}</p>
                        <p class="text-gray-400 text-sm truncate">{{ $song['artist'] }}</p>
                    </div>
                </div>

                {{-- Album --}}
                <div class="text-gray-400 text-sm truncate">
                    {{ $song['album'] }}
                </div>

                {{-- Played At Time --}}
                <div class="text-gray-400 text-sm">
                    {{ $song['played_at'] }}
                </div>

                {{-- Actions & Duration --}}
                <div class="flex items-center justify-end gap-3">
                    {{-- Add to Queue --}}
                    <button wire:click.stop="addToQueue({{ $song['id'] }})"
                        class="text-gray-400 hover:text-white opacity-0 group-hover:opacity-100 transition"
                        title="Add to Queue">
                        <svg class="w-5 h-5 hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                        </svg>
                    </button>
                    <span class="text-gray-400 text-sm">{{ $song['duration_formatted'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
        @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20">
            <div class="w-32 h-32 bg-[#282828] rounded-full flex items-center justify-center mb-6">
                <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-white mb-2">Belum ada history</h3>
            <p class="text-gray-400 mb-6">Mulai dengarkan lagu untuk melihat history kamu di sini!</p>
            <a href="{{ route('home') }}"
                class="px-6 py-3 bg-white text-black font-semibold rounded-full hover:scale-105 transition">
                Cari Lagu
            </a>
        </div>
        @endif
    </div>
</div>