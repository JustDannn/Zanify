<div class="w-64 bg-[#0f0f0f] text-gray-200 h-screen flex flex-col">

    {{-- TITLE --}}
    <div class="px-4 py-4 flex items-center justify-between">
        <h2 class="font-semibold text-lg">Your Library</h2>
        <button class="p-1 hover:bg-gray-800 rounded">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
            </svg>
        </button>
    </div>

    {{-- FILTER BUTTONS --}}
    <div class="flex gap-2 px-4 pb-2">
        <button class="px-3 py-1 bg-[#1f1f1f] rounded-full text-sm">Playlists</button>
        <button class="px-3 py-1 bg-[#1f1f1f] rounded-full text-sm">Podcasts</button>
        <button class="px-3 py-1 bg-[#1f1f1f] rounded-full text-sm">Artists</button>
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
    <div class="overflow-y-auto flex-1 px-2">

        @foreach ($items as $item)
        <div class="flex items-center gap-3 px-2 py-2 rounded hover:bg-[#1a1a1a] cursor-pointer">

            {{-- IMAGE --}}
            <img src="https://api.dicebear.com/9.x/shapes/svg?seed={{ $item['title'] }}"
                class="w-12 h-12 rounded {{ $item['type'] === 'artist' ? 'rounded-full' : '' }}">

            <div>
                <div class="font-medium truncate">{{ $item['title'] }}</div>
                <div class="text-sm text-gray-400 truncate">{{ $item['subtitle'] }}</div>
            </div>

        </div>
        @endforeach

    </div>

</div>