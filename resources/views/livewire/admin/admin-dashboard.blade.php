<div class="min-h-screen  bg-black flex" x-data="{ activeTab: 'songs' }">

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
                <div x-data="{ openEditSongs: false }">
                    <!-- Tombol -->
                    <button @click="openEditSongs = true"
                        class="bg-[#16a349] text-white px-5 py-2 rounded-full hover:bg-[#34a857] cursor-pointer font-semibold">
                        + Add Songs
                    </button>

                    <!-- Modal using Teleport to body -->
                    <template x-teleport="body">
                        <div x-show="openEditSongs" x-cloak class="fixed inset-0 z-[100]">
                            <!-- Modal Overlay -->
                            <div x-show="openEditSongs" x-transition.opacity @click="openEditSongs = false"
                                class="fixed inset-0 bg-black/70"></div>

                            <!-- Modal Box -->
                            <div x-show="openEditSongs" x-transition
                                class="fixed inset-0 flex items-center justify-center pointer-events-none overflow-y-auto py-4">
                                <div @click.stop
                                    class="w-[900px] max-h-[85vh] overflow-y-auto overflow-x-visible bg-[#0e0e0e] rounded-2xl p-8 shadow-xl border border-white/10 pointer-events-auto">

                                    <!-- HEADER -->
                                    <div class="flex justify-between items-center mb-6">
                                        <h2 class="text-2xl text-white font-semibold">Upload New Songs</h2>

                                        <button class="text-white text-2xl hover:text-red-400"
                                            @click="openEditSongs = false">
                                            &times;
                                        </button>
                                    </div>

                                    <!-- LIVEWIRE POST COMPONENT -->
                                    @livewire('admin.post')

                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </div>
        </div>


        {{-- Tabs --}}
        <div class="flex gap-6 border-b border-gray-800 mb-6">
            <button @click="activeTab = 'songs'" class="pb-2 font-semibold transition"
                :class="activeTab === 'songs' ? 'border-b-2 border-white text-white' : 'text-gray-500 hover:text-gray-300'">
                Songs
            </button>
            <button @click="activeTab = 'albums'" class="pb-2 font-semibold transition"
                :class="activeTab === 'albums' ? 'border-b-2 border-white text-white' : 'text-gray-500 hover:text-gray-300'">
                Albums
            </button>
            <button @click="activeTab = 'artists'" class="pb-2 font-semibold transition"
                :class="activeTab === 'artists' ? 'border-b-2 border-white text-white' : 'text-gray-500 hover:text-gray-300'">
                Artists
            </button>
        </div>


        {{-- SONGS TAB CONTENT --}}
        <div x-show="activeTab === 'songs'" x-cloak>
            {{-- TABLE --}}
            <table class="w-full table-fixed">
                <thead class="text-left text-gray-500 text-sm border-b border-gray-800">
                    <tr>
                        <th class="pb-3 font-medium">Name</th>
                        <th class="pb-3 font-medium w-20">Streams</th>
                        <th class="pb-3 font-medium w-20">Listeners</th>
                        <th class="pb-3 font-medium w-16">Saves</th>
                        <th class="pb-3 font-medium w-20">Duration</th>
                        <th class="pb-3 font-medium w-28">Release date</th>
                        <th class="pb-3 font-medium text-right w-20">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($songs as $song)
                    <tr class="border-b border-gray-800/50 hover:bg-white/5 transition-colors group">
                        {{-- Cover & Name --}}
                        <td class="py-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $song->cover_url }}" alt="{{ $song->title }}"
                                    class="w-12 h-12 rounded-lg object-cover bg-gray-800">
                                <div class="min-w-0">
                                    <div class="font-semibold text-white text-base truncate">{{ $song->title }}</div>
                                    <div class="text-gray-500 text-sm truncate">{{ $song->artist_display }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 text-gray-300">{{ number_format($song->play_count) }}</td>
                        <td class="py-4 text-gray-300">{{ number_format($song->listeners) }}</td>
                        <td class="py-4 text-gray-300">{{ number_format($song->save_count) }}</td>
                        <td class="py-4 text-gray-300">{{ $song->duration_formatted }}</td>
                        <td class="py-4 text-gray-400">
                            @if($song->release_date)
                            @if($song->release_date->isToday())
                            Today
                            @elseif($song->release_date->isYesterday())
                            Yesterday
                            @elseif($song->release_date->diffInDays(now()) < 7) {{ $song->
                                release_date->diffInDays(now()) }}
                                days ago
                                @else
                                {{ $song->release_date->format('M j, Y') }}
                                @endif
                                @else
                                -
                                @endif
                        </td>

                        {{-- Actions --}}
                        <td class="py-4 text-right">
                            <div
                                class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                {{-- Edit Button --}}
                                <button wire:click="editSong({{ $song->id }})"
                                    class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Delete Button --}}
                                <button wire:click="confirmDelete({{ $song->id }})"
                                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-500/10 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                <p class="text-lg font-medium">No songs uploaded yet</p>
                                <p class="text-sm mt-1">Click "+ Add Songs" to upload your first track</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ALBUMS TAB CONTENT --}}
        <div x-show="activeTab === 'albums'" x-cloak>
            @livewire('admin.album-manager')
        </div>

        {{-- ARTISTS TAB CONTENT --}}
        <div x-show="activeTab === 'artists'" x-cloak>
            @livewire('admin.artist-manager')
        </div>

    </main>

    {{-- ==================== EDIT SONG MODAL ==================== --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-[100]">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/70" wire:click="closeEditModal"></div>

        {{-- Modal Box --}}
        <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[600px] bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto">
                <h3 class="text-xl text-white font-semibold mb-6">Edit Song</h3>

                <form wire:submit.prevent="updateSong">
                    <div class="flex gap-6">
                        {{-- Cover Upload --}}
                        <div class="flex-shrink-0">
                            <label class="block text-gray-400 text-sm mb-2">Cover Image</label>
                            <div
                                class="w-32 h-32 bg-gray-800 rounded-xl flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer">
                                @if($editCover)
                                <img src="{{ $editCover->temporaryUrl() }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                                @elseif($editingSong && $editingSong->cover)
                                <img src="{{ $editingSong->cover_url }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                                @else
                                <svg class="w-10 h-10 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                @endif

                                <label for="edit-cover"
                                    class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs font-medium py-2 text-center cursor-pointer hover:bg-black/90 transition">
                                    {{ $editCover || ($editingSong && $editingSong->cover) ? 'CHANGE' : 'ADD COVER' }}
                                </label>
                                <input type="file" id="edit-cover" class="hidden" wire:model="editCover"
                                    accept="image/*">
                            </div>
                            <div wire:loading wire:target="editCover" class="mt-2 text-center">
                                <span class="text-xs text-gray-400">Uploading...</span>
                            </div>
                            @error('editCover') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Form Fields --}}
                        <div class="flex-1 space-y-4">
                            {{-- Title --}}
                            <div>
                                <label class="block text-gray-400 text-sm mb-2">Song Title</label>
                                <input type="text" wire:model="editForm.title"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                                @error('editForm.title') <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Artist (Searchable with Tags) --}}
                            <div class="relative" x-data="{ showSuggestions: false, searchText: '' }">
                                <label class="block text-gray-400 text-sm mb-2">Artists</label>

                                {{-- Selected Artists Tags --}}
                                @if(!empty($editSelectedArtists))
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach($editSelectedArtists as $artistIndex => $selectedArtist)
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-sm">
                                        {{ $selectedArtist['name'] }}
                                        <button type="button" wire:click="removeEditArtist({{ $artistIndex }})"
                                            class="hover:text-red-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                <input type="text" placeholder="Search for artists..." x-model="searchText"
                                    x-on:input.debounce.300ms="$wire.searchEditArtists(searchText)"
                                    @focus="showSuggestions = true" @click.away="showSuggestions = false"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">

                                {{-- Artist Suggestions Dropdown --}}
                                @if(!empty($editArtistSuggestions))
                                <div x-show="showSuggestions" x-cloak
                                    class="absolute z-50 w-full mt-1 bg-[#282828] rounded-lg shadow-xl border border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                                    @foreach($editArtistSuggestions as $suggestion)
                                    <button type="button" wire:click="selectEditArtist({{ $suggestion['id'] }})"
                                        @click="searchText = ''; showSuggestions = false"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-[#383838] transition text-left">
                                        @if($suggestion['photo'])
                                        <img src="{{ $suggestion['photo'] }}"
                                            class="w-10 h-10 rounded-full object-cover">
                                        @else
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                        @endif
                                        <span class="text-white">{{ $suggestion['name'] }}</span>
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            {{-- Release Date --}}
                            <div>
                                <label class="block text-gray-400 text-sm mb-2">Release Date</label>
                                <input type="date" wire:model="editForm.release_date"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                                @error('editForm.release_date') <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Album Selection (Searchable) --}}
                            <div class="relative" x-data="{ showAlbumSuggestions: false, albumSearchText: '' }">
                                <label class="block text-gray-400 text-sm mb-2">Album</label>

                                @if($editSelectedAlbum)
                                {{-- Selected Album Display --}}
                                <div
                                    class="flex items-center gap-3 bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3">
                                    @if($editSelectedAlbum->cover)
                                    <img src="{{ $editSelectedAlbum->cover_url }}"
                                        class="w-10 h-10 rounded object-cover">
                                    @else
                                    <div class="w-10 h-10 rounded bg-gray-700 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="flex-1">
                                        <span class="text-white">{{ $editSelectedAlbum->title }}</span>
                                        <span class="text-gray-500 text-sm block">{{ $editSelectedAlbum->artist_name
                                            }}</span>
                                    </div>
                                    <button type="button" wire:click="clearEditAlbum"
                                        class="text-gray-400 hover:text-red-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                @else
                                {{-- Search Input --}}
                                <input type="text" placeholder="Search for album or leave empty for Single..."
                                    x-model="albumSearchText"
                                    x-on:input.debounce.300ms="$wire.searchEditAlbums(albumSearchText)"
                                    @focus="showAlbumSuggestions = true" @click.away="showAlbumSuggestions = false"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">

                                {{-- Album Suggestions Dropdown --}}
                                @if(count($editAlbumSuggestions) > 0)
                                <div x-show="showAlbumSuggestions" x-cloak
                                    class="absolute z-50 w-full mt-1 bg-[#282828] rounded-lg shadow-xl border border-gray-700 overflow-hidden max-h-48 overflow-y-auto">
                                    @foreach($editAlbumSuggestions as $album)
                                    <button type="button" wire:click="selectEditAlbum({{ $album->id }})"
                                        @click="albumSearchText = ''; showAlbumSuggestions = false"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-[#383838] transition text-left">
                                        @if($album->cover)
                                        <img src="{{ $album->cover_url }}" class="w-10 h-10 rounded object-cover">
                                        @else
                                        <div class="w-10 h-10 rounded bg-gray-700 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        @endif
                                        <div>
                                            <span class="text-white">{{ $album->title }}</span>
                                            <span class="text-gray-500 text-sm block">{{ $album->artist_name }}</span>
                                        </div>
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" wire:click="closeEditModal"
                            class="px-5 py-2.5 text-gray-400 hover:text-white transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="updateSong,editCover"
                            class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateSong">Save Changes</span>
                            <span wire:loading wire:target="updateSong">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ==================== DELETE CONFIRMATION MODAL ==================== --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[100]">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/70" wire:click="closeDeleteModal"></div>

        {{-- Modal Box --}}
        <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
            <div
                class="w-[400px] bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto text-center">
                {{-- Warning Icon --}}
                <div class="w-16 h-16 mx-auto mb-4 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl text-white font-semibold mb-2">Delete Song?</h3>
                <p class="text-gray-400 mb-6">
                    Are you sure you want to delete <span class="text-white font-medium">"{{ $deletingSongTitle
                        }}"</span>?
                    This action cannot be undone.
                </p>

                {{-- Buttons --}}
                <div class="flex gap-3 justify-center">
                    <button type="button" wire:click="closeDeleteModal"
                        class="px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="deleteSong"
                        class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Flash Messages --}}
    @if(session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="fixed top-6 right-6 z-[200] bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="fixed top-6 right-6 z-[200] bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg">
        {{ session('error') }}
    </div>
    @endif
</div>