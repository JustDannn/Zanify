<div x-data="{ open: false }" x-on:open-upload-modal.window="open = true" x-on:close-upload-modal.window="open = false"
    class="relative">
    <div class="p-8">
        <h2 class="text-2xl font-semibold text-white mb-6">New Songs</h2>

        <div x-data="{ isDragging: false }" @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="isDragging = false; $wire.set('songFiles', Object.values($event.dataTransfer.files), false)"
            class="border-2 p-10 rounded-lg transition"
            :class="{ 'border-green-500 bg-[#174e27]': isDragging, 'border-gray-700 bg-[#303030]': !isDragging }"
            wire:target="songFiles">

            <input type="file" multiple id="song-upload-input" class="hidden" wire:model="songFiles">

            <div class="text-center">
                <p class="text-xl font-medium text-white mb-4">
                    Drag your songs here
                </p>
                <p class="text-gray-400 mb-6">
                    .mp3 or .wav, max. 100MB
                </p>

                <label for="song-upload-input"
                    class="bg-white text-black px-5 py-2 rounded-full cursor-pointer hover:bg-gray-200 transition font-semibold">
                    SELECT FILES
                </label>
            </div>
        </div>

        <div wire:loading wire:target="songFiles"
            class="mt-4 p-4 bg-gray-700 rounded-lg flex items-center justify-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-white">Uploading... Please wait.</span>
        </div>


        <div class="mt-8 space-y-4">
            @foreach ($uploadedSongs as $index => $song)
            <div class="bg-gray-800 p-4 rounded-lg flex justify-between items-start border border-gray-700">

                <div class="flex flex-1 space-x-4">

                    <div class="w-24 h-24 bg-blue-400 rounded-lg flex flex-col justify-end overflow-hidden relative">
                        <div class="absolute inset-0 flex items-center justify-center opacity-70">
                            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19V6l12-3v13.5M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3">
                                </path>
                            </svg>
                        </div>
                        <label for="cover-{{ $index }}"
                            class="text-sm text-center text-white py-1 bg-black bg-opacity-50 hover:bg-opacity-75 cursor-pointer">
                            COVER UPLOAD
                        </label>
                        <input type="file" id="cover-{{ $index }}" class="hidden"
                            wire:model="uploadedSongs.{{ $index }}.cover">
                    </div>

                    <div class="flex-1 grid grid-cols-3 gap-3">
                        <div class="col-span-3">
                            <input type="text" placeholder="Song name"
                                class="w-full bg-gray-700 text-white rounded-md border-gray-600 focus:ring-[#16a349] focus:border-[#16a349] p-2.5"
                                wire:model.defer="uploadedSongs.{{ $index }}.name">
                        </div>

                        <input type="text" placeholder="Label"
                            class="w-full bg-gray-700 text-white rounded-md border-gray-600 focus:ring-[#16a349] focus:border-[#16a349] p-2.5"
                            wire:model.defer="uploadedSongs.{{ $index }}.label">

                        <select
                            class="w-full bg-gray-700 text-gray-400 rounded-md border-gray-600 focus:ring-[#16a349] focus:border-[#16a349] p-2.5"
                            wire:model.defer="uploadedSongs.{{ $index }}.genre">
                            <option value="">Select genre</option>
                            <option value="pop">Pop</option>
                            <option value="rock">Rock</option>
                        </select>

                        <div class="col-span-3">
                            <input type="text" placeholder="Add tags (comma separated)"
                                class="w-full bg-gray-700 text-white rounded-md border-gray-600 focus:ring-[#16a349] focus:border-[#16a349] p-2.5"
                                wire:model.defer="uploadedSongs.{{ $index }}.tags">
                        </div>
                    </div>
                </div>

                <div class="ml-4 flex flex-col items-end space-y-2">
                    <span class="text-sm font-medium text-yellow-500">DRAFT</span>

                    <button class="text-gray-400 hover:text-red-500" wire:click="removeSong({{ $index }})">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.93a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.39-.018.78-.04 1.17-.066m12.398-.137c.363-.045.724-.076 1.082-.095M5 12h.01M19 12h.01M7 7V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3m-3 7v4m-4-4v4" />
                        </svg>

                    </button>
                    <button class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            <button class="bg-[#16a349] text-white px-5 py-2 rounded-full hover:bg-[#34a857]" wire:click="saveAllSongs">
                Save All Songs
            </button>
        </div>
    </div>
</div>