<div>
    {{-- ==================== ALBUMS GRID ==================== --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        {{-- Add Album Card --}}
        <div wire:click="openCreateModal"
            class="aspect-square bg-[#161616] hover:bg-[#1e1e1e] rounded-xl border-2 border-dashed border-gray-700 hover:border-green-500/50 flex flex-col items-center justify-center cursor-pointer transition-all group">
            <div
                class="w-16 h-16 bg-gray-800 group-hover:bg-green-500/20 rounded-full flex items-center justify-center mb-4 transition-all">
                <svg class="w-8 h-8 text-gray-500 group-hover:text-green-500 transition-colors" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span class="text-gray-400 group-hover:text-white font-medium transition-colors">Create Album</span>
        </div>

        {{-- Album Cards --}}
        @foreach($albums as $album)
        <div class="group relative">
            <div class="aspect-square bg-[#161616] rounded-xl overflow-hidden relative">
                {{-- Cover Image --}}
                <img src="{{ $album->cover_url }}" alt="{{ $album->title }}" class="w-full h-full object-cover">

                {{-- Hover Overlay --}}
                <div
                    class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                    <button wire:click="editAlbum({{ $album->id }})"
                        class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button wire:click="confirmDelete({{ $album->id }})"
                        class="w-10 h-10 bg-red-500/30 hover:bg-red-500/50 rounded-full flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>

                {{-- Song Count Badge --}}
                <div class="absolute top-3 right-3 bg-black/70 text-white text-xs font-medium px-2 py-1 rounded-full">
                    {{ $album->songs_count }} {{ Str::plural('song', $album->songs_count) }}
                </div>
            </div>

            {{-- Album Info --}}
            <div class="mt-3">
                <h3 class="text-white font-semibold truncate">{{ $album->title }}</h3>
                <p class="text-gray-400 text-sm truncate">{{ $album->artist_name }}</p>
                @if($album->year)
                <p class="text-gray-500 text-xs">{{ $album->year }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($albums->hasPages())
    <div class="mt-6 flex items-center justify-between">
        <div class="text-gray-400 text-sm">
            Showing {{ $albums->firstItem() }} to {{ $albums->lastItem() }} of {{ $albums->total() }} albums
        </div>
        <div class="flex items-center gap-2">
            @if($albums->onFirstPage())
            <span class="px-4 py-2 bg-gray-800 text-gray-500 rounded-lg cursor-not-allowed">Previous</span>
            @else
            <button wire:click="previousPage"
                class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">Previous</button>
            @endif

            <div class="flex items-center gap-1">
                @foreach($albums->getUrlRange(max(1, $albums->currentPage() - 2), min($albums->lastPage(),
                $albums->currentPage() + 2)) as $page => $url)
                <button wire:click="gotoPage({{ $page }})"
                    class="w-10 h-10 rounded-lg transition {{ $page == $albums->currentPage() ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
                    {{ $page }}
                </button>
                @endforeach
            </div>

            @if($albums->hasMorePages())
            <button wire:click="nextPage"
                class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">Next</button>
            @else
            <span class="px-4 py-2 bg-gray-800 text-gray-500 rounded-lg cursor-not-allowed">Next</span>
            @endif
        </div>
    </div>
    @endif

    {{-- Empty State --}}
    @if($albums->isEmpty())
    <div class="text-center py-16">
        <div class="w-20 h-20 mx-auto mb-4 bg-gray-800/50 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <p class="text-gray-400 text-lg font-medium">No albums yet</p>
        <p class="text-gray-500 text-sm mt-1">Click "Create Album" to add your first album</p>
    </div>
    @endif


    {{-- ==================== CREATE ALBUM MODAL ==================== --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-100">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/70" wire:click="closeCreateModal"></div>

        {{-- Modal Box --}}
        <div
            class="fixed inset-0 flex items-center justify-center pointer-events-none overflow-y-auto scrollbar-hide py-8">
            <div
                class="w-[700px] max-h-[90vh] overflow-y-auto scrollbar-hide bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto">
                <h3 class="text-xl text-white font-semibold mb-6">Create New Album</h3>

                {{-- Upload Error --}}
                @if($uploadError)
                <div class="mb-4 p-3 bg-red-600/20 border border-red-500 text-red-400 rounded-lg text-sm">
                    {{ $uploadError }}
                </div>
                @endif

                <form wire:submit.prevent="createAlbum">
                    <div class="flex gap-6">
                        {{-- Cover Upload --}}
                        <div class="shrink-0">
                            <label class="block text-gray-400 text-sm mb-2">Album Cover</label>
                            <div
                                class="w-40 h-40 bg-gray-800 rounded-xl flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer border-2 border-dashed border-gray-600 hover:border-green-500/50 transition-colors">
                                @if($createCover)
                                <img src="{{ $createCover->temporaryUrl() }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                                <div
                                    class="absolute top-2 right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                @else
                                <svg class="w-12 h-12 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                @endif

                                <label for="create-album-cover"
                                    class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs font-medium py-2 text-center cursor-pointer hover:bg-black/90 transition">
                                    {{ $createCover ? 'CHANGE COVER' : 'ADD COVER' }}
                                </label>
                                <input type="file" id="create-album-cover" class="hidden" wire:model="createCover"
                                    accept="image/jpeg,image/png,image/gif,image/webp">
                            </div>
                            <div wire:loading wire:target="createCover" class="mt-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-400">Uploading...</span>
                                </div>
                            </div>
                            @error('createCover') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Form Fields --}}
                        <div class="flex-1 space-y-4">
                            {{-- Title --}}
                            <div>
                                <label class="block text-gray-400 text-sm mb-2">Album Title *</label>
                                <input type="text" wire:model="createForm.title"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none"
                                    placeholder="Enter album title">
                                @error('createForm.title') <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Artist (Searchable) --}}
                            <div x-data="{ open: false, searchText: '' }" @click.away="open = false" class="relative">
                                <label class="block text-gray-400 text-sm mb-2">Artist *</label>

                                @if($createSelectedArtist)
                                {{-- Selected Artist Display --}}
                                <div
                                    class="flex items-center gap-3 bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3">
                                    @if($createSelectedArtist->photo)
                                    <img src="{{ $createSelectedArtist->photo_url }}"
                                        class="w-8 h-8 rounded-full object-cover">
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                    @endif
                                    <span class="text-white flex-1">{{ $createSelectedArtist->name }}</span>
                                    <button type="button" wire:click="clearCreateArtist"
                                        class="text-gray-400 hover:text-red-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                @else
                                {{-- Search Input --}}
                                <input type="text" x-model="searchText"
                                    x-on:input.debounce.300ms="$wire.set('createArtistSearch', searchText)"
                                    @focus="open = true"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none"
                                    placeholder="Search for artist...">

                                {{-- Suggestions Dropdown --}}
                                @if(count($createArtistSuggestions) > 0)
                                <div x-show="open"
                                    class="absolute z-50 w-full mt-1 bg-[#1a1a1a] border border-gray-700 rounded-lg overflow-hidden shadow-xl max-h-48 overflow-y-auto scrollbar-hide">
                                    @foreach($createArtistSuggestions as $artist)
                                    <button type="button" wire:click="selectCreateArtist({{ $artist->id }})"
                                        @click="open = false; searchText = ''"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/10 transition text-left">
                                        @if($artist->photo)
                                        <img src="{{ $artist->photo_url }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                        @endif
                                        <span class="text-white">{{ $artist->name }}</span>
                                    </button>
                                    @endforeach
                                </div>
                                @elseif(strlen($createArtistSearch) >= 2)
                                <div x-show="open && searchText.length >= 2"
                                    class="absolute z-50 w-full mt-1 bg-[#1a1a1a] border border-gray-700 rounded-lg p-4 text-center text-gray-500">
                                    No artists found. Create one in "Artists" tab first.
                                </div>
                                @endif
                                @endif

                                @error('createSelectedArtist') <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Year --}}
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Year</label>
                                    <input type="number" wire:model="createForm.year" min="1900" max="2100"
                                        class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none"
                                        placeholder="{{ date('Y') }}">
                                    @error('createForm.year') <span class="text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Release Date --}}
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Release Date</label>
                                    <input type="date" wire:model="createForm.release_date"
                                        class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                                    @error('createForm.release_date') <span class="text-red-400 text-sm">{{ $message
                                        }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Song Selection --}}
                    <div class="mt-6">
                        <label class="block text-gray-400 text-sm mb-3">Add Songs to Album (Optional)</label>
                        <div
                            class="bg-[#1a1a1a] rounded-lg border border-gray-700 max-h-48 overflow-y-auto scrollbar-hide">
                            @forelse($availableSongs as $song)
                            <label
                                class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 cursor-pointer border-b border-gray-800 last:border-0">
                                <input type="checkbox" wire:model="selectedSongs" value="{{ $song->id }}"
                                    class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-green-500 focus:ring-green-500 focus:ring-offset-0">
                                <img src="{{ $song->cover_url }}" alt=""
                                    class="w-10 h-10 rounded object-cover bg-gray-800">
                                <div class="flex-1 min-w-0">
                                    <div class="text-white font-medium truncate">{{ $song->title }}</div>
                                    <div class="text-gray-500 text-sm truncate">{{ $song->artist_display }}</div>
                                </div>
                            </label>
                            @empty
                            <div class="px-4 py-6 text-center text-gray-500">
                                No songs available. Upload songs first.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" wire:click="closeCreateModal"
                            class="px-5 py-2.5 text-gray-400 hover:text-white transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="createAlbum,createCover"
                            class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="createAlbum">Create Album</span>
                            <span wire:loading wire:target="createAlbum">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


    {{-- ==================== EDIT ALBUM MODAL ==================== --}}
    @if($showEditModal && $editingAlbum)
    <div class="fixed inset-0 z-100">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black/70" wire:click="closeEditModal"></div>

        {{-- Modal Box --}}
        <div
            class="fixed inset-0 flex items-center justify-center pointer-events-none overflow-y-auto scrollbar-hide py-8">
            <div
                class="w-[700px] max-h-[90vh] overflow-y-auto scrollbar-hide bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto">
                <h3 class="text-xl text-white font-semibold mb-6">Edit Album</h3>

                <form wire:submit.prevent="updateAlbum">
                    <div class="flex gap-6">
                        {{-- Cover Upload --}}
                        <div class="shrink-0">
                            <label class="block text-gray-400 text-sm mb-2">Album Cover</label>
                            <div
                                class="w-40 h-40 bg-gray-800 rounded-xl flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer">
                                @if($editCover)
                                <img src="{{ $editCover->temporaryUrl() }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                                @elseif($editingAlbum->cover)
                                <img src="{{ $editingAlbum->cover_url }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                                @else
                                <svg class="w-12 h-12 text-gray-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                @endif

                                <label for="edit-album-cover"
                                    class="absolute bottom-0 left-0 right-0 bg-black/70 text-white text-xs font-medium py-2 text-center cursor-pointer hover:bg-black/90 transition">
                                    {{ $editCover || $editingAlbum->cover ? 'CHANGE' : 'ADD COVER' }}
                                </label>
                                <input type="file" id="edit-album-cover" class="hidden" wire:model="editCover"
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
                                <label class="block text-gray-400 text-sm mb-2">Album Title *</label>
                                <input type="text" wire:model="editForm.title"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none"
                                    placeholder="Enter album title">
                                @error('editForm.title') <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Artist (Searchable) --}}
                            <div x-data="{ open: false, searchText: '{{ $editSelectedArtist ? '' : $editArtistSearch }}' }"
                                @click.away="open = false" class="relative">
                                <label class="block text-gray-400 text-sm mb-2">Artist *</label>

                                @if($editSelectedArtist)
                                {{-- Selected Artist Display --}}
                                <div
                                    class="flex items-center gap-3 bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3">
                                    @if($editSelectedArtist->photo)
                                    <img src="{{ $editSelectedArtist->photo_url }}"
                                        class="w-8 h-8 rounded-full object-cover">
                                    @else
                                    <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    </div>
                                    @endif
                                    <span class="text-white flex-1">{{ $editSelectedArtist->name }}</span>
                                    <button type="button" wire:click="clearEditArtist"
                                        class="text-gray-400 hover:text-red-400 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                @else
                                {{-- Search Input --}}
                                <input type="text" x-model="searchText"
                                    x-on:input.debounce.300ms="$wire.set('editArtistSearch', searchText)"
                                    @focus="open = true"
                                    class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none"
                                    placeholder="Search for artist...">

                                {{-- Suggestions Dropdown --}}
                                @if(count($editArtistSuggestions) > 0)
                                <div x-show="open"
                                    class="absolute z-50 w-full mt-1 bg-[#1a1a1a] border border-gray-700 rounded-lg overflow-hidden shadow-xl max-h-48 overflow-y-auto scrollbar-hide">
                                    @foreach($editArtistSuggestions as $artist)
                                    <button type="button" wire:click="selectEditArtist({{ $artist->id }})"
                                        @click="open = false; searchText = ''"
                                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-white/10 transition text-left">
                                        @if($artist->photo)
                                        <img src="{{ $artist->photo_url }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                        <div
                                            class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                            </svg>
                                        </div>
                                        @endif
                                        <span class="text-white">{{ $artist->name }}</span>
                                    </button>
                                    @endforeach
                                </div>
                                @elseif(strlen($editArtistSearch) >= 2)
                                <div x-show="open && searchText.length >= 2"
                                    class="absolute z-50 w-full mt-1 bg-[#1a1a1a] border border-gray-700 rounded-lg p-4 text-center text-gray-500">
                                    No artists found. Create one in "Artists" tab first.
                                </div>
                                @endif
                                @endif

                                @error('editSelectedArtist') <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Year --}}
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Year</label>
                                    <input type="number" wire:model="editForm.year" min="1900" max="2100"
                                        class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                                    @error('editForm.year') <span class="text-red-400 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Release Date --}}
                                <div>
                                    <label class="block text-gray-400 text-sm mb-2">Release Date</label>
                                    <input type="date" wire:model="editForm.release_date"
                                        class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                                    @error('editForm.release_date') <span class="text-red-400 text-sm">{{ $message
                                        }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Song Selection --}}
                    <div class="mt-6">
                        <label class="block text-gray-400 text-sm mb-3">Songs in Album</label>
                        <div
                            class="bg-[#1a1a1a] rounded-lg border border-gray-700 max-h-48 overflow-y-auto scrollbar-hide">
                            @forelse($allSongs as $song)
                            <label
                                class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 cursor-pointer border-b border-gray-800 last:border-0">
                                <input type="checkbox" wire:model="editSelectedSongs" value="{{ $song->id }}"
                                    class="w-4 h-4 rounded bg-gray-700 border-gray-600 text-green-500 focus:ring-green-500 focus:ring-offset-0">
                                <img src="{{ $song->cover_url }}" alt=""
                                    class="w-10 h-10 rounded object-cover bg-gray-800">
                                <div class="flex-1 min-w-0">
                                    <div class="text-white font-medium truncate">{{ $song->title }}</div>
                                    <div class="text-gray-500 text-sm truncate">{{ $song->artist_display }}</div>
                                </div>
                                @if($song->album_id && $song->album_id != $editingAlbum->id)
                                <span class="text-xs text-yellow-500 bg-yellow-500/10 px-2 py-1 rounded">
                                    In: {{ $song->album?->title }}
                                </span>
                                @endif
                            </label>
                            @empty
                            <div class="px-4 py-6 text-center text-gray-500">
                                No songs available.
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" wire:click="closeEditModal"
                            class="px-5 py-2.5 text-gray-400 hover:text-white transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="updateAlbum,editCover"
                            class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateAlbum">Save Changes</span>
                            <span wire:loading wire:target="updateAlbum">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif


    {{-- ==================== DELETE CONFIRMATION MODAL ==================== --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-100">
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

                <h3 class="text-xl text-white font-semibold mb-2">Delete Album?</h3>
                <p class="text-gray-400 mb-2">
                    Are you sure you want to delete <span class="text-white font-medium">"{{ $deletingAlbumTitle
                        }}"</span>?
                </p>
                <p class="text-gray-500 text-sm mb-6">
                    Songs in this album will not be deleted, but will become singles.
                </p>

                {{-- Buttons --}}
                <div class="flex gap-3 justify-center">
                    <button type="button" wire:click="closeDeleteModal"
                        class="px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="deleteAlbum"
                        class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>