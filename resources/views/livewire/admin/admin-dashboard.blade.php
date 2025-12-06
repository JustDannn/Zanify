<div class="min-h-screen  bg-black flex">

    {{-- SIDEBAR --}}
    <aside class="w-60 bg-[#0f0f0f] shadow-lg p-6">
        <div class="text-2xl text-gray-200  font-bold mb-8">
            Zanify Dashboard
        </div>

        <nav class="space-y-4 text-gray-200">
            <a href="#" class="block text-lg font-semibold">My Library</a>
            <a href="#" class="block text-lg font-semibold">Samples</a>
            <a href="#" class="block text-lg font-semibold">Stats</a>
        </nav>

        <div class="absolute bottom-6 left-6 text-sm text-gray-500">
            Help
        </div>
    </aside>


    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-10">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-white">My Library</h1>

            <div class="flex gap-4">
                {{-- Search --}}
                <div class="relative">
                    <input type="text" class="w-64 bg-[#161616] rounded-full px-4 py-2 text-gray-200"
                        placeholder="Search library...">
                    <span class="absolute right-3 top-2.5 text-gray-200"> <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </span>
                </div>

                {{-- Upload Button --}}
                <div x-data="{ openEditSongs: false }" x-on:open-edit-songs.window="openEditSongs = true"
                    x-on:close-edit-songs.window="openEditSongs = false">
                    <!-- Tombol -->
                    <button @click="openEditSongs = true"
                        class="bg-[#16a349] text-white px-5 py-2 rounded-full hover:bg-[#34a857] cursor-pointer font-semibold">
                        + Add Songs
                    </button>

                    <!-- Modal Overlay -->
                    <div x-show="openEditSongs" x-transition.opacity class="fixed inset-0 bg-black/70 z-40"></div>

                    <!-- Modal Box -->
                    <div x-show="openEditSongs" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center">
                        <div
                            class="w-[900px] max-h-[90vh] overflow-y-auto bg-[#0e0e0e] rounded-2xl p-8 shadow-xl border border-white/10">

                            <!-- HEADER -->
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl text-white font-semibold">Upload New Songs</h2>

                                <button class="text-white text-2xl hover:text-red-400" @click="openEditSongs = false">
                                    &times;
                                </button>
                            </div>

                            <!-- LIVEWIRE POST COMPONENT (FULL CONTENT) -->
                            @livewire('admin.post')

                        </div>
                    </div>
                </div>

            </div>
        </div>


        {{-- Tabs --}}
        <div class="flex gap-6 border-b mb-6">
            <button class="pb-2 border-b-2 border-black font-semibold">Released</button>
            <button class="pb-2 text-gray-500">Upcoming</button>
        </div>


        {{-- TABLE --}}
        <table class="w-full">
            <thead class="text-left text-gray-300 text-sm">
                <tr>
                    <th class="pb-3">Name</th>
                    <th class="pb-3">Streams</th>
                    <th class="pb-3">Listeners</th>
                    <th class="pb-3">Saves</th>
                    <th class="pb-3">Release date</th>
                </tr>
            </thead>

            <tbody class="text-gray-800 text-sm">
                @forelse($songs as $song)
                <tr class="border-t">
                    {{-- Cover & Name --}}
                    <td class="py-4 flex items-center gap-4">
                        <img src="{{ $song->cover_url }}" class="w-12 h-12 rounded object-cover">
                        <div>
                            <div class="font-semibold text-lg">{{ $song->title }}</div>
                            <div class="text-gray-500 text-sm">{{ $song->artist }}</div>
                        </div>
                    </td>

                    <td class="py-4">{{ number_format($song->streams) }}</td>
                    <td class="py-4">{{ number_format($song->listeners) }}</td>
                    <td class="py-4">{{ number_format($song->saves) }}</td>
                    <td class="py-4">
                        {{ $song->release_date->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-500">
                        No songs uploaded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </main>
</div>