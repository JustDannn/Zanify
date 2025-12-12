<div class="space-y-8 pb-32">
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
            $isClickableSong = $item['type'] === 'song';
            $linkUrl = match($item['type']) {
            'album' => route('album', $item['id']),
            'artist' => route('artist', $item['id']),
            default => null
            };
            @endphp

            @if($linkUrl)
            <a href="{{ $linkUrl }}" class="shrink-0 w-[180px] group cursor-pointer no-underline">
                @else
                <div class="shrink-0 w-[180px] group cursor-pointer" @if($isClickableSong)
                    wire:click="playSong({{ $item['id'] }}, {{ json_encode(collect($section['items'])->where('type', 'song')->pluck('id')->toArray()) }})"
                    @endif>
                    @endif

                    {{-- Image Container --}}
                    <div class="relative mb-3">
                        <div
                            class="{{ ($item['is_rounded'] ?? false) ? 'rounded-full' : 'rounded-lg' }} overflow-hidden aspect-square bg-[#282828] shadow-lg">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                class="w-full h-full object-cover" loading="lazy">
                        </div>

                        {{-- Play Button (for songs and albums) --}}
                        @if(in_array($item['type'], ['song', 'album']))
                        <div
                            class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            <button
                                class="w-12 h-12 bg-green-500 hover:bg-green-400 hover:scale-105 rounded-full flex items-center justify-center shadow-xl transition">
                                <svg class="w-5 h-5 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z" />
                                </svg>
                            </button>
                        </div>
                        @endif

                        {{-- New Badge --}}
                        @if($item['is_new'] ?? false)
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-0.5 bg-blue-500 text-white text-xs font-bold rounded">NEW</span>
                        </div>
                        @endif
                    </div>

                    {{-- Title --}}
                    <p class="text-white font-semibold text-sm truncate group-hover:text-white/90">
                        {{ $item['title'] }}
                    </p>

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

                    @if($linkUrl)
            </a>
            @else
        </div>
        @endif
        @endforeach
    </div>
</div>
@endforeach

@if(empty($sections))
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