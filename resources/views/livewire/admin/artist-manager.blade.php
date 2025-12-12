<div>
    {{-- Header with Add Button --}}
    <div class="flex justify-between items-center mb-6">
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search"
                class="w-64 bg-[#161616] border border-gray-700 rounded-lg px-4 py-2 text-gray-200 focus:border-green-500 focus:outline-none"
                placeholder="Search artists...">
            <span class="absolute right-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
        </div>

        <button wire:click="openCreateModal"
            class="bg-green-600 text-white px-5 py-2 rounded-full hover:bg-green-700 transition font-semibold flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Artist
        </button>
    </div>

    {{-- Artists Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        @forelse($artists as $artist)
        <div class="bg-[#181818] rounded-lg p-4 hover:bg-[#282828] transition group">
            {{-- Photo --}}
            <div class="relative mb-4">
                <img src="{{ $artist->photo_url }}" alt="{{ $artist->name }}"
                    class="w-full aspect-square rounded-full object-cover shadow-lg">

                {{-- Actions Overlay --}}
                <div
                    class="absolute inset-0 bg-black/60 rounded-full opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                    <button wire:click="editArtist({{ $artist->id }})"
                        class="p-2 bg-white/20 rounded-full hover:bg-white/30 transition">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button wire:click="confirmDelete({{ $artist->id }})"
                        class="p-2 bg-red-500/20 rounded-full hover:bg-red-500/40 transition">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <h3 class="text-white font-semibold truncate text-center">{{ $artist->name }}</h3>
            <p class="text-gray-400 text-sm text-center mt-1">
                {{ $artist->songs_count }} songs • {{ $artist->albums_count }} albums
            </p>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <div class="text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <p class="text-lg font-medium">No artists yet</p>
                <p class="text-sm mt-1">Click "Add Artist" to create your first artist</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($artists->hasPages())
    <div class="mt-6">
        {{ $artists->links() }}
    </div>
    @endif

    {{-- ==================== CREATE MODAL ==================== --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-100">
        <div class="fixed inset-0 bg-black/70" wire:click="closeCreateModal"></div>

        <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[500px] bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl text-white font-semibold">Add New Artist</h3>
                    <button wire:click="closeCreateModal"
                        class="text-gray-400 hover:text-white text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="createArtist">
                    {{-- Photo Upload --}}
                    <div class="flex justify-center mb-6">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-gray-800 overflow-hidden">
                                @if($createPhoto)
                                <img src="{{ $createPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <label for="create-photo"
                                class="absolute bottom-0 right-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-green-700 transition">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </label>
                            <input type="file" id="create-photo" class="hidden" wire:model="createPhoto"
                                accept="image/*">
                        </div>
                    </div>
                    <div wire:loading wire:target="createPhoto" class="text-center text-gray-400 text-sm mb-4">
                        Uploading photo...
                    </div>
                    @error('createPhoto') <p class="text-red-400 text-sm text-center mb-4">{{ $message }}</p> @enderror

                    {{-- Name --}}
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">Artist Name *</label>
                        <input type="text" wire:model="createName"
                            class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none"
                            placeholder="Enter artist name">
                        @error('createName') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bio --}}
                    <div class="mb-6">
                        <label class="block text-gray-400 text-sm mb-2">Biography</label>
                        <textarea wire:model="createBio" rows="3"
                            class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none resize-none"
                            placeholder="Tell us about this artist..."></textarea>
                        @error('createBio') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end">
                        <button type="button" wire:click="closeCreateModal"
                            class="px-5 py-2.5 text-gray-400 hover:text-white transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="createArtist,createPhoto"
                            class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="createArtist">Create Artist</span>
                            <span wire:loading wire:target="createArtist">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ==================== EDIT MODAL ==================== --}}
    @if($showEditModal && $editingArtist)
    <div class="fixed inset-0 z-100">
        <div class="fixed inset-0 bg-black/70" wire:click="closeEditModal"></div>

        <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-[500px] bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl text-white font-semibold">Edit Artist</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-white text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="updateArtist">
                    {{-- Photo Upload --}}
                    <div class="flex justify-center mb-6">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-gray-800 overflow-hidden">
                                @if($editPhoto)
                                <img src="{{ $editPhoto->temporaryUrl() }}" class="w-full h-full object-cover">
                                @else
                                <img src="{{ $editingArtist->photo_url }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <label for="edit-photo"
                                class="absolute bottom-0 right-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-green-700 transition">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </label>
                            <input type="file" id="edit-photo" class="hidden" wire:model="editPhoto" accept="image/*">
                        </div>
                    </div>
                    <div wire:loading wire:target="editPhoto" class="text-center text-gray-400 text-sm mb-4">
                        Uploading photo...
                    </div>
                    @error('editPhoto') <p class="text-red-400 text-sm text-center mb-4">{{ $message }}</p> @enderror

                    {{-- Name --}}
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2">Artist Name *</label>
                        <input type="text" wire:model="editForm.name"
                            class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none">
                        @error('editForm.name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bio --}}
                    <div class="mb-6">
                        <label class="block text-gray-400 text-sm mb-2">Biography</label>
                        <textarea wire:model="editForm.bio" rows="3"
                            class="w-full bg-[#1a1a1a] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-green-500 focus:outline-none resize-none"></textarea>
                        @error('editForm.bio') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-3 justify-end">
                        <button type="button" wire:click="closeEditModal"
                            class="px-5 py-2.5 text-gray-400 hover:text-white transition">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="updateArtist,editPhoto"
                            class="px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                            <span wire:loading.remove wire:target="updateArtist">Save Changes</span>
                            <span wire:loading wire:target="updateArtist">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- ==================== DELETE MODAL ==================== --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-100">
        <div class="fixed inset-0 bg-black/70" wire:click="closeDeleteModal"></div>

        <div class="fixed inset-0 flex items-center justify-center pointer-events-none">
            <div
                class="w-[400px] bg-[#0e0e0e] rounded-2xl p-6 shadow-xl border border-white/10 pointer-events-auto text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl text-white font-semibold mb-2">Delete Artist?</h3>
                <p class="text-gray-400 mb-6">
                    Are you sure you want to delete <span class="text-white font-medium">"{{ $deletingArtistName
                        }}"</span>?
                    This will not delete their songs or albums.
                </p>

                <div class="flex gap-3 justify-center">
                    <button wire:click="closeDeleteModal"
                        class="px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button wire:click="deleteArtist"
                        class="px-5 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>