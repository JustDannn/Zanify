<div x-data="playerController()" x-show="isVisible" x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
    @song-loaded.window="loadSong($event.detail)"
    class="fixed bottom-0 left-0 right-0 h-[90px] bg-black border-t border-[#282828] z-50 px-4" x-init="
        Livewire.on('song-loaded', (data) => {
            console.log('Song loaded event received:', data);
            loadSong(data[0] || data);
        });
    ">

    <div class="h-full flex items-center justify-between max-w-full">
        {{-- LEFT: Song Info --}}
        <div class="flex items-center gap-4 w-[30%] min-w-[180px]">
            <img :src="cover || 'https://via.placeholder.com/56x56/1a1a1a/666?text=♪'" :alt="title"
                class="w-14 h-14 rounded object-cover">
            <div class="min-w-0">
                <p class="text-white text-sm font-medium truncate" x-text="title || 'No song playing'"></p>
                <p class="text-gray-400 text-xs truncate" x-text="artist || '-'"></p>
            </div>
            {{-- Like button (connected to Livewire) --}}
            <button wire:click="toggleLike" class="text-gray-400 hover:text-white transition ml-2 hover:scale-110">
                @if($isLiked)
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
                @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                @endif
            </button>
        </div>

        {{-- CENTER: Player Controls --}}
        <div class="flex flex-col items-center w-[40%] max-w-[722px]">
            {{-- Control Buttons --}}
            <div class="flex items-center gap-4 mb-2">
                {{-- Shuffle --}}
                <button class="text-gray-400 hover:text-white transition" :class="{ 'text-green-500': shuffle }"
                    @click="toggleShuffle()" title="Shuffle - Find similar songs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z" />
                    </svg>
                </button>
                {{-- Previous --}}
                <button @click="playPrevious()" class="text-gray-400 hover:text-white transition" title="Previous">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 6h2v12H6zm3.5 6l8.5 6V6z" />
                    </svg>
                </button>
                {{-- Play/Pause --}}
                <button @click="togglePlay()"
                    class="w-9 h-9 bg-white rounded-full flex items-center justify-center hover:scale-105 transition">
                    <svg x-show="!isPlaying" class="w-4 h-4 text-black ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg>
                    <svg x-show="isPlaying" class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z" />
                    </svg>
                </button>
                {{-- Next --}}
                <button @click="playNext()" class="text-gray-400 hover:text-white transition" title="Next">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z" />
                    </svg>
                </button>
                {{-- Repeat - 3 states: off, all (queue), one (single song) --}}
                <button class="text-gray-400 hover:text-white transition relative"
                    :class="{ 'text-green-500': repeatMode !== 'off' }" @click="toggleRepeat()">
                    {{-- Repeat icon --}}
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z" />
                    </svg>
                    {{-- "1" indicator for repeat one mode --}}
                    <span x-show="repeatMode === 'one'"
                        class="absolute -top-1 -right-1 text-[10px] font-bold text-green-500">1</span>
                </button>
            </div>
            {{-- Progress Bar --}}
            <div class="flex items-center gap-2 w-full">
                <span class="text-gray-400 text-xs w-10 text-right" x-text="formatTime(currentTime)">0:00</span>
                <div class="flex-1 group relative h-1">
                    <input type="range" min="0" :max="duration || 100" x-model="currentTime"
                        @input="seek($event.target.value)" class="w-full h-1 bg-transparent rounded-lg appearance-none cursor-pointer absolute inset-0 z-10
                               [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 
                               [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:opacity-0
                               [&::-webkit-slider-thumb]:group-hover:opacity-100 [&::-webkit-slider-thumb]:transition
                               [&::-webkit-slider-runnable-track]:bg-transparent">
                    {{-- Background track --}}
                    <div class="absolute inset-0 bg-[#4d4d4d] rounded-full"></div>
                    {{-- Progress fill --}}
                    <div class="absolute top-0 left-0 h-full bg-white rounded-full group-hover:bg-green-500 transition-colors"
                        :style="'width: ' + (duration ? (currentTime / duration * 100) : 0) + '%'"></div>
                </div>
                <span class="text-gray-400 text-xs w-10" x-text="formatTime(duration)">0:00</span>
            </div>
        </div>

        {{-- RIGHT: Volume & Other Controls --}}
        <div class="flex items-center justify-end gap-3 w-[30%] min-w-[180px]">
            {{-- Now Playing View --}}
            <button class="text-gray-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M15 6H3v2h12V6zm0 4H3v2h12v-2zM3 16h8v-2H3v2zM17 6v8.18c-.31-.11-.65-.18-1-.18-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3V8h3V6h-5z" />
                </svg>
            </button>
            {{-- Queue --}}
            <button class="text-gray-400 hover:text-white transition" @click="$dispatch('toggle-queue')" title="Queue">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" />
                </svg>
            </button>
            {{-- Volume --}}
            <button class="text-gray-400 hover:text-white transition" @click="toggleMute()">
                <svg x-show="volume > 50" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z" />
                </svg>
                <svg x-show="volume > 0 && volume <= 50" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M18.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM5 9v6h4l5 5V4L9 9H5z" />
                </svg>
                <svg x-show="volume === 0" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z" />
                </svg>
            </button>
            <div class="w-24 group relative">
                <input type="range" min="0" max="100" x-model="volume" @input="setVolume($event.target.value)" class="w-full h-1 bg-[#4d4d4d] rounded-lg appearance-none cursor-pointer
                              [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-3 [&::-webkit-slider-thumb]:h-3 
                              [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white">
            </div>
            {{-- Fullscreen --}}
            <button class="text-gray-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Hidden Audio Element --}}
    <audio x-ref="audio" @timeupdate="currentTime = $refs.audio.currentTime" @ended="handleEnded()"
        @loadedmetadata="duration = $refs.audio.duration" preload="metadata"></audio>
</div>

<script>
    function playerController() {
        return {
            isVisible: false,
            isPlaying: false,
            currentTime: 0,
            duration: 0,
            volume: 70,
            previousVolume: 70,
            liked: false,
            shuffle: false,
            repeatMode: 'off', // 'off', 'all' (circular queue), 'one' (single song)
            title: null,
            artist: null,
            cover: null,
            audioUrl: null,
            songId: null,

            init() {
                // Set initial volume
                this.$nextTick(() => {
                    if (this.$refs.audio) {
                        this.$refs.audio.volume = this.volume / 100;
                    }
                });

                // Listen for repeat mode changes from Livewire
                Livewire.on('repeat-mode-changed', (data) => {
                    this.repeatMode = data.mode || data[0]?.mode || 'off';
                });

                // Listen for shuffle mode changes from Livewire
                Livewire.on('shuffle-mode-changed', (data) => {
                    this.shuffle = data.mode || data[0]?.mode || false;
                });

                // Listen for playback ended (no more songs)
                Livewire.on('playback-ended', () => {
                    this.isPlaying = false;
                });
            },

            loadSong(data) {
                console.log('loadSong called with:', data);

                // Handle nested array from Livewire
                if (Array.isArray(data)) {
                    data = data[0];
                }

                if (!data) {
                    console.error('No data received');
                    return;
                }

                this.songId = data.id;
                this.title = data.title;
                this.artist = data.artist;
                this.cover = data.cover;
                this.audioUrl = data.audioUrl;
                this.duration = data.duration || 0;
                this.isVisible = true;
                this.currentTime = 0;

                console.log('Audio URL:', this.audioUrl);

                this.$nextTick(() => {
                    if (this.$refs.audio && this.audioUrl) {
                        this.$refs.audio.src = this.audioUrl;
                        this.$refs.audio.load();
                        this.$refs.audio.play().then(() => {
                            this.isPlaying = true;
                            console.log('Playing started');
                        }).catch(e => {
                            console.error('Error playing audio:', e);
                            this.isPlaying = false;
                        });
                    } else {
                        console.error('No audio ref or URL', this.$refs.audio, this.audioUrl);
                    }
                });
            },

            togglePlay() {
                if (!this.$refs.audio || !this.audioUrl) return;

                if (this.isPlaying) {
                    this.$refs.audio.pause();
                    this.isPlaying = false;
                } else {
                    this.$refs.audio.play().then(() => {
                        this.isPlaying = true;
                    }).catch(e => {
                        console.error('Error playing:', e);
                    });
                }
            },

            seek(time) {
                if (this.$refs.audio) {
                    this.$refs.audio.currentTime = time;
                }
            },

            setVolume(value) {
                this.volume = parseInt(value);
                if (this.$refs.audio) {
                    this.$refs.audio.volume = this.volume / 100;
                }
            },

            toggleMute() {
                if (this.volume > 0) {
                    this.previousVolume = this.volume;
                    this.volume = 0;
                } else {
                    this.volume = this.previousVolume || 70;
                }
                this.setVolume(this.volume);
            },

            playNext() {
                // Request next song from queue
                Livewire.dispatch('request-next-song');
            },

            playPrevious() {
                // If more than 3 seconds in, restart current song
                if (this.currentTime > 3) {
                    this.$refs.audio.currentTime = 0;
                } else {
                    // Otherwise, play previous song from history (traverse prev pointer)
                    Livewire.dispatch('request-previous-song');
                }
            },

            /**
             * Toggle repeat mode: off -> all -> one -> off
             * - off: no repeat
             * - all: repeat entire queue/playlist (circular doubly linked list)
             * - one: repeat current song only
             */
            toggleRepeat() {
                const modes = ['off', 'all', 'one'];
                const currentIndex = modes.indexOf(this.repeatMode);
                const nextIndex = (currentIndex + 1) % modes.length;
                this.repeatMode = modes[nextIndex];

                // Sync with Livewire Queue component
                Livewire.dispatch('toggle-repeat');
            },

            /**
             * Toggle shuffle mode on/off
             * When enabled, will find similar songs using fuzzy matching by artist
             */
            toggleShuffle() {
                this.shuffle = !this.shuffle;
                
                // Sync with Livewire Queue component
                Livewire.dispatch('toggle-shuffle');
            },

            handleEnded() {
                // Repeat One: loop current song
                if (this.repeatMode === 'one') {
                    this.$refs.audio.currentTime = 0;
                    this.$refs.audio.play();
                    return;
                }

                // For 'off' and 'all' modes, request next song
                // Queue component handles circular logic for 'all' mode
                this.isPlaying = false;
                Livewire.dispatch('request-next-song');
            },

            formatTime(seconds) {
                if (!seconds || isNaN(seconds)) return '0:00';
                const mins = Math.floor(seconds / 60);
                const secs = Math.floor(seconds % 60);
                return `${mins}:${secs.toString().padStart(2, '0')}`;
            }
        }
    }
</script>