    <div class="p-6 text-white">

        <h1 class="text-4xl font-bold">{{ $playlist['title'] }}</h1>

        <p class="text-gray-400 mt-2">
            {{ $playlist['description'] }}
        </p>

        <p class="mt-1 text-sm">
            {{ $playlist['subtitle'] }}
        </p>

        <p class="mt-1 text-sm">
            Made for {{ $playlist['made_for'] }} —
            {{ $playlist['total_songs'] }} songs,
            {{ $playlist['duration'] }}
        </p>

    </div>
