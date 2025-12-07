<div class="relative" x-data="{ 
    isDragging: false,
    uploadProgress: 0
}" 
x-on:livewire-upload-start="uploadProgress = 0"
x-on:livewire-upload-progress="uploadProgress = $event.detail.progress"
x-on:livewire-upload-finish="uploadProgress = 100"
>
    {{-- Error Message --}}
    @if ($uploadError)
    <div class="mb-4 p-4 bg-red-600/20 border border-red-500 text-red-400 rounded-lg">
        <strong>Error:</strong> {{ $uploadError }}
    </div>
    @endif

    {{-- Success Message --}}
    @if (session()->has('success'))
    <div class="mb-4 p-4 bg-green-600/20 border border-green-500 text-green-400 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    {{-- ==================== DRAG & DROP AREA ==================== --}}
    <div 
        @dragover.prevent="isDragging = true" 
        @dragleave.prevent="isDragging = false" 
        @drop.prevent="
            isDragging = false; 
            $refs.fileInput.files = $event.dataTransfer.files;
            $refs.fileInput.dispatchEvent(new Event('change'));
        "
        class="border-2 border-dashed rounded-xl p-8 text-center transition-all duration-300"
        :class="isDragging ? 'border-green-500 bg-green-500/10' : 'border-gray-600 bg-transparent hover:border-gray-500'"
    >
        <input 
            type="file" 
            multiple 
            x-ref="fileInput"
            wire:model="songFiles"
            class="hidden" 
            accept=".mp3,.wav"
        >

        <div class="flex flex-col md:flex-row items-center justify-center gap-4">
            <div class="text-center md:text-left">
                <p class="text-white font-medium text-lg">Drag your songs here</p>
                <p class="text-gray-500 text-sm">.mp3 ou .wav, max. 100MB</p>
            </div>
            
            <span class="text-gray-500">or</span>
            
            <button 
                type="button" 
                @click="$refs.fileInput.click()"
                class="px-6 py-2.5 bg-white text-black font-semibold rounded-lg hover:bg-gray-200 transition"
            >
                SELECT FILES
            </button>
        </div>

        {{-- Selected files preview (before upload) --}}
        @if($songFiles && count($songFiles) > 0)
        <div class="mt-4 flex flex-wrap gap-2 justify-center">
            @foreach($songFiles as $file)
            <span class="px-3 py-1 bg-gray-700 text-white text-sm rounded-full">
                {{ $file->getClientOriginalName() }}
            </span>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ==================== UPLOAD PROGRESS BAR ==================== --}}
    <div wire:loading wire:target="songFiles" class="mt-4">
        <div class="flex items-center gap-3 mb-2">
            <svg class="animate-spin h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-white text-sm">Uploading files...</span>
            <span class="text-gray-400 text-sm ml-auto" x-text="uploadProgress + '%'"></span>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
            <div class="bg-green-500 h-2 rounded-full transition-all duration-300" :style="'width: ' + uploadProgress + '%'"></div>
        </div>
    </div>

    {{-- Processing to Azure --}}
    @if ($isUploading)
    <div class="mt-4 p-4 bg-blue-500/20 border border-blue-500 rounded-lg">
        <div class="flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-blue-400">Processing & uploading to cloud storage...</span>
        </div>
    </div>
    @endif

    {{-- ==================== EMPTY STATE ==================== --}}
    @if (count($uploadedSongs) === 0 && !$isUploading)
    <div class="mt-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-gray-800 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
        </div>
        <h3 class="text-white font-medium text-lg">Hi, Welcome to <span class="text-green-500">Zanify</span>!</h3>
        <p class="text-gray-500 text-sm mt-1">Start uploading your songs right here.</p>
    </div>
    @endif

    {{-- ==================== UPLOADED SONGS LIST (METADATA EDIT) ==================== --}}
    @if (count($uploadedSongs) > 0)
    <div class="mt-8 space-y-4">
        @foreach ($uploadedSongs as $index => $song)
        <div class="bg-[#1a1a1a] rounded-xl overflow-hidden" x-data="{ expanded: true }">
            {{-- Song Header --}}
            <div class="flex items-center justify-between p-4 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    {{-- Loading/Success indicator --}}
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-white font-medium">{{ $song['original_name'] }}</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-2 text-gray-400 text-sm">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        DRAFT
                    </span>
                    
                    {{-- Actions --}}
                    <button wire:click="removeSong({{ $index }})" class="p-2 text-gray-400 hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                    
                    <button @click="expanded = !expanded" class="p-2 text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Metadata Form (Expandable) --}}
            <div x-show="expanded" x-collapse class="p-4">
                <div class="flex gap-6">
                    {{-- Cover Upload --}}
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex flex-col items-center justify-center relative overflow-hidden group cursor-pointer">
                            @if(isset($song['cover']) && $song['cover'])
                                <img src="{{ $song['cover']->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <svg class="w-12 h-12 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13.5M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            @endif
                            
                            <label for="cover-{{ $index }}" class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs font-medium py-2 text-center cursor-pointer hover:bg-black/80 transition">
                                COVER UPLOAD
                            </label>
                            <input type="file" id="cover-{{ $index }}" class="hidden" wire:model="uploadedSongs.{{ $index }}.cover" accept="image/*">
                        </div>
                    </div>

                    {{-- Metadata Fields --}}
                    <div class="flex-1 space-y-3">
                        {{-- Song Name --}}
                        <input 
                            type="text" 
                            placeholder="Song name"
                            wire:model.defer="uploadedSongs.{{ $index }}.name"
                            class="w-full bg-[#2a2a2a] text-white placeholder-gray-500 rounded-lg border-0 px-4 py-3 focus:ring-2 focus:ring-green-500"
                        >
                        
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Label --}}
                            <input 
                                type="text" 
                                placeholder="Label"
                                wire:model.defer="uploadedSongs.{{ $index }}.label"
                                class="w-full bg-[#2a2a2a] text-white placeholder-gray-500 rounded-lg border-0 px-4 py-3 focus:ring-2 focus:ring-green-500"
                            >
                            
                            {{-- Genre --}}
                            <select 
                                wire:model.defer="uploadedSongs.{{ $index }}.genre"
                                class="w-full bg-[#2a2a2a] text-gray-400 rounded-lg border-0 px-4 py-3 focus:ring-2 focus:ring-green-500"
                            >
                                <option value="">Select genre</option>
                                <option value="pop">Pop</option>
                                <option value="rock">Rock</option>
                                <option value="hiphop">Hip Hop</option>
                                <option value="rnb">R&B</option>
                                <option value="electronic">Electronic</option>
                                <option value="jazz">Jazz</option>
                                <option value="classical">Classical</option>
                                <option value="country">Country</option>
                                <option value="indie">Indie</option>
                                <option value="metal">Metal</option>
                            </select>
                        </div>
                        
                        {{-- Tags --}}
                        <input 
                            type="text" 
                            placeholder="Add tags"
                            wire:model.defer="uploadedSongs.{{ $index }}.tags"
                            class="w-full bg-[#2a2a2a] text-white placeholder-gray-500 rounded-lg border-0 px-4 py-3 focus:ring-2 focus:ring-green-500"
                        >
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Save Button --}}
    <div class="mt-6 flex justify-end">
        <button 
            wire:click="saveAllSongs"
            wire:loading.attr="disabled"
            wire:target="saveAllSongs"
            class="px-8 py-3 bg-green-600 text-white font-semibold rounded-full hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove wire:target="saveAllSongs">Save All Songs</span>
            <span wire:loading wire:target="saveAllSongs" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Saving...
            </span>
        </button>
    </div>
    @endif
</div>
