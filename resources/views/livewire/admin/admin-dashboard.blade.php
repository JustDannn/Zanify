<div>
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">My Library</h1>

        <button class="px-4 py-2 bg-pink-500 text-white rounded-full hover:bg-pink-600">
            + Upload music
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-6 mb-6 border-b">
        <button class="pb-2 border-b-2 border-black font-semibold">Released</button>
        <button class="pb-2 text-gray-500">Upcoming</button>
    </div>

    {{-- Table --}}
    <table class="w-full">
        <thead class="text-gray-500 text-sm">
            <tr>
                <th class="text-left pb-3">Name</th>
                <th class="text-left pb-3">Streams</th>
                <th class="text-left pb-3">Listeners</th>
                <th class="text-left pb-3">Saves</th>
                <th class="text-left pb-3">Release date</th>
            </tr>
        </thead>

        <tbody class="text-sm">
            @foreach($songs as $song)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-4 flex items-center gap-3">
                    <img src="{{ asset('storage/'.$song->cover) }}" class="w-12 h-12 rounded">
                    <div>
                        <p class="font-medium">{{ $song->title }}</p>
                        <p class="text-gray-500 text-xs">{{ $song->artist }}</p>
                    </div>
                </td>

                <td>{{ number_format($song->streams) }}</td>
                <td>{{ number_format($song->listeners) }}</td>
                <td>{{ number_format($song->saves) }}</td>
                <td>{{ $song->release_date->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>