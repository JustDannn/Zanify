<div class="space-y-8 pb-32">
    {{-- Recommended Stations Section (like Spotify Radio) --}}
    @if(!empty($recommendedStations))
    <div class="px-6">
        <div class="flex justify-between items-end mb-4">
            <h2 class="text-2xl font-bold text-white underline decoration-2 underline-offset-4">Recommended Stations
            </h2>
            @if(count($recommendedStations) > 5)
            <a href="#" class="text-sm font-bold text-gray-400 hover:underline hover:text-white transition">Show all</a>
            @endif
        </div>

        <div class="flex space-x-4 pb-4 overflow-x-auto scrollbar-hide">
            @php
            $gradients = [
            'from-green-400 to-green-600',
            'from-purple-400 to-purple-600',
            'from-pink-400 to-pink-600',
            'from-blue-400 to-blue-600',
            'from-orange-400 to-orange-600',
            'from-red-400 to-red-600',
            'from-teal-400 to-teal-600',
            'from-indigo-400 to-indigo-600',
            'from-yellow-400 to-yellow-600',
            'from-cyan-400 to-cyan-600',
            'from-rose-400 to-rose-600',
            'from-emerald-400 to-emerald-600',
            ];
            @endphp
            @foreach($recommendedStations as $index => $station)
            @php
            $gradient = $gradients[$index % count($gradients)];
            @endphp
            <div class="shrink-0 w-[180px] group cursor-pointer"
                wire:click="playStation({{ $station['id'] }}, {{ json_encode($station['artist_ids']) }})">

                {{-- Radio Card with Random Color Accent --}}
                <div class="relative mb-3">
                    {{-- Dynamic gradient background --}}
                    <div
                        class="rounded-lg overflow-hidden aspect-square bg-gradient-to-br {{ $gradient }} shadow-lg relative">
                        {{-- Artist images collage --}}
                        <div class="absolute inset-0 flex items-center justify-center p-4">
                            <div class="relative w-full h-full">
                                {{-- Main artist image (circular) --}}
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div
                                        class="w-28 h-28 rounded-full overflow-hidden border-4 border-black/20 shadow-xl">
                                        <img src="{{ $station['image'] }}" alt="{{ $station['title'] }}"
                                            class="w-full h-full object-cover" loading="lazy">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Radio badge --}}
                        <div
                            class="absolute top-2 right-2 flex items-center gap-1 bg-black/40 backdrop-blur-sm px-2 py-1 rounded-full">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                            <span class="text-white text-xs font-bold">RADIO</span>
                        </div>
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

                {{-- Station Title (Artist Name) --}}
                <p class="text-white font-semibold text-sm truncate group-hover:text-white/90">
                    {{ $station['title'] }}
                </p>

                {{-- Related Artists --}}
                <p class="text-gray-400 text-sm truncate mt-0.5">
                    {{ $station['subtitle'] }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Regular Sections --}}
    @foreach($sections as $section)
    <div class="px-6">
        {{-- Section Header --}}
        <div class="flex justify-between items-end mb-4">
            <h2 class="text-2xl font-bold text-white">{{ $section['title'] }}</h2>
            @if(count($section['items']) > 5)
            <a href="#" class="text-sm font-bold text-gray-400 hover:underline hover:text-white transition">Show all</a>
            @endif
        </div>

        {{-- Horizontal Scroll Cards --}}
        <div class="flex space-x-4 pb-4 overflow-x-auto scrollbar-hide">
            @foreach($section['items'] as $item)
            @php
            $linkUrl = match($item['type']) {
            'album' => route('album', $item['id']),
            'artist' => route('artist', $item['id']),
            default => null
            };
            @endphp

            <div class="shrink-0 w-[180px] group">
                {{-- Image Container with Play Button --}}
                <div class="relative mb-3">
                    {{-- Clickable Image - navigates to detail --}}
                    @if($linkUrl)
                    <a href="{{ $linkUrl }}" wire:navigate class="block">
                        <div
                            class="{{ ($item['is_rounded'] ?? false) ? 'rounded-full' : 'rounded-lg' }} overflow-hidden aspect-square bg-[#282828] shadow-lg cursor-pointer">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                class="w-full h-full object-cover" loading="lazy">
                        </div>
                    </a>
                    @else
                    <div
                        class="{{ ($item['is_rounded'] ?? false) ? 'rounded-full' : 'rounded-lg' }} overflow-hidden aspect-square bg-[#282828] shadow-lg">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover"
                            loading="lazy">
                    </div>
                    @endif

                    {{-- Play Button for albums - OUTSIDE the link --}}
                    @if($item['type'] === 'album')
                    <button wire:click="playAlbum({{ $item['id'] }})"
                        class="absolute bottom-2 right-2 w-12 h-12 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center shadow-xl transition opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 z-10">
                        <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </button>
                    @endif

                    {{-- New Badge --}}
                    @if($item['is_new'] ?? false)
                    <div class="absolute top-2 left-2 pointer-events-none">
                        <span class="px-2 py-0.5 bg-blue-500 text-white text-xs font-bold rounded">NEW</span>
                    </div>
                    @endif
                </div>

                {{-- Title - clickable --}}
                @if($linkUrl)
                <a href="{{ $linkUrl }}" wire:navigate class="block">
                    <p class="text-white font-semibold text-sm truncate group-hover:text-white/90 cursor-pointer">{{
                        $item['title'] }}</p>
                </a>
                @else
                <p class="text-white font-semibold text-sm truncate">{{ $item['title'] }}</p>
                @endif

                {{-- Subtitle --}}
                <p class="text-gray-400 text-sm truncate mt-0.5">
                    @if($item['type'] === 'artist')
                    Artist
                    @elseif($item['type'] === 'album' && isset($item['year']))
                    {{ $item['year'] }} • {{ $item['subtitle'] }}
                    @else
                    {{ $item['subtitle'] }}
                    @endif
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if(empty($sections) && empty($recommendedStations))
    <div class="px-6 py-12 text-center">
        <div class="w-20 h-20 mx-auto mb-4 bg-[#282828] rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
            </svg>
        </div>
        <h3 class="text-white text-xl font-bold mb-2">No music yet</h3>
        <p class="text-gray-400">Upload some songs via the admin panel to get started!</p>
    </div>
    @endif
</div>