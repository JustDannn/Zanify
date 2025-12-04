<div class="flex items-end gap-6">

    {{-- COVER --}}
    <img 
        src="{{ $playlist['cover'] }}" 
        class="w-48 h-48 rounded shadow-lg"
        alt="playlist cover"
    >

    <div class="flex flex-col">
        <span class="text-sm text-gray-300">Public Playlist</span>

        <h1 class="text-6xl font-bold text-white mt-2">
            {{ $playlist['title'] }}
        </h1>

        <p class="text-gray-300 mt-2">
            {{ $playlist['subtitle'] }}
        </p>

        <p class="text-gray-400 mt-1 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M21.57 10.66a1.58 1.58..." />
            </svg>

            Made for 
            <span class="text-white font-semibold">{{ $playlist['made_for'] }}</span>
            · 
            {{ $playlist['count'] }}, 
            {{ $playlist['duration'] }}
        </p>

        <p class="text-gray-400 text-sm mt-1">
            About recommendations and the impact of promotion
        </p>
    </div>

</div>
