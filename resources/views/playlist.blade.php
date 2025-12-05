<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zanify - Struktur Data Project</title>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #121212; }
        ::-webkit-scrollbar-thumb { background: #5a5a5a; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #888; }
        .spotify-green { color: #1DB954; }
        .bg-spotify-green { background-color: #1DB954; }
        .hover-spotify-green:hover { color: #1DB954; }
        .text-xxs { font-size: 0.65rem; }
        .playing-indicator { color: #1DB954; }
    </style>
</head>
<body class="bg-black text-white h-screen flex flex-col overflow-hidden font-sans">

    <!-- AREA UTAMA: 3 KOLOM -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- KOLOM 1: SIDEBAR KIRI (Menu) -->
        <aside class="w-64 bg-black flex flex-col gap-2 p-2 hidden md:flex shrink-0">
            <div class="bg-[#121212] rounded-lg p-5 flex flex-col gap-5">
                <div class="flex items-center gap-4 cursor-pointer text-white">
                    <i class="fa-solid fa-house text-xl"></i>
                    <span class="font-bold">Home</span>
                </div>
                <div class="flex items-center gap-4 cursor-pointer text-gray-400 hover:text-white transition">
                    <i class="fa-solid fa-magnifying-glass text-xl"></i>
                    <span class="font-bold">Search (Genre)</span>
                </div>
            </div>
            <div class="bg-[#121212] rounded-lg p-4 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-4 text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-lines-leaning text-xl"></i>
                        <span class="font-bold">Library</span>
                    </div>
                </div>
                <!-- Indikator Struktur Data (Sesuai BAB III) -->
                <div class="space-y-2">
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-purple-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">Library Utama</h4>
                        <p class="text-white font-bold text-xs">Doubly Linked List</p>
                    </div>
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-green-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">Playlist User</h4>
                        <p class="text-white font-bold text-xs">Circular Doubly LL</p>
                    </div>
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-blue-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">Queue (Next Up)</h4>
                        <p class="text-white font-bold text-xs">Queue (FIFO)</p>
                    </div>
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-red-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">History</h4>
                        <p class="text-white font-bold text-xs">Stack (LIFO)</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- KOLOM 2: MAIN CONTENT (Playlist / Linked List) -->
        <main class="flex-1 bg-[#121212] rounded-lg my-2 overflow-y-auto relative scroll-smooth mx-1">
            <!-- Header -->
            <div class="bg-gradient-to-b from-green-900 to-[#121212] p-6 pb-8">
                <div class="flex items-end gap-6 pt-4">
                    <div class="w-40 h-40 bg-gradient-to-br from-gray-800 to-black shadow-2xl flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-compact-disc text-6xl text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-white">User Playlist</p>
                        <h1 class="text-5xl font-black mb-4 tracking-tighter">Circular Mix</h1>
                        <p class="text-gray-300 text-sm font-medium">
                            Playlist ini menggunakan <strong>Circular Doubly Linked List</strong> (Looping).<br>
                            Data merujuk ke Library Utama (<strong>Doubly Linked List</strong>).
                        </p>
                        <div class="flex items-center gap-2 text-sm font-bold text-white mt-2">
                            <span>Total Nodes: {{ $total }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="px-6 bg-[#121212]/30 backdrop-blur-sm sticky top-0 z-10 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="main-play-button" class="w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center text-black hover:scale-105 shadow-lg">
                        <i class="fa-solid fa-play text-lg pl-1" id="main-play-icon"></i>
                    </button>
                    <!-- TOMBOL ADD TO LINKED LIST -->
                    <button onclick="addRandomSong()" class="border border-gray-500 text-gray-300 px-4 py-2 rounded-full hover:border-white hover:text-white hover:scale-105 transition text-xs font-bold uppercase">
                        <i class="fa-solid fa-plus mr-2"></i> Add Node
                    </button>
                </div>
            </div>

            <!-- Song List Table -->
            <div class="px-6 pb-10">
                <div class="grid grid-cols-[auto_1fr_1fr_auto] gap-4 px-4 py-2 text-gray-400 border-b border-[#2a2a2a] text-xs mb-2">
                    <div class="w-6 text-center">#</div>
                    <div>Title</div>
                    <div class="hidden sm:block">Artist</div>
                    <div class="text-right"><i class="fa-regular fa-clock"></i></div>
                </div>

                <div id="playlist-container">
                    @forelse ($songs as $index => $song)
                    <div class="song-row group grid grid-cols-[auto_1fr_1fr_auto] gap-4 px-4 py-2 hover:bg-[#2a2a2a] rounded-md items-center text-sm text-gray-400 transition" 
                        data-id="{{ $song['id'] }}" 
                        data-url="{{ $song['audio_url'] ?? '' }}"
                        data-title="{{ $song['title'] }}"
                        data-artist="{{ $song['artist'] }}">
                        
                        <div class="w-6 text-center relative h-6 flex items-center justify-center">
                            <span class="row-index group-hover:hidden text-xs">{{ $index + 1 }}</span>
                            <span class="row-playing-indicator hidden text-xs playing-indicator absolute group-hover:hidden"><i class="fa-solid fa-volume-high"></i></span>
                            <!-- PLAY BUTTON: Moves to History (Stack) -->
                            <button onclick="startPlayback('{{ $song['id'] }}', '{{ $song['audio_url'] ?? '' }}', '{{ $song['title'] }}', '{{ $song['artist'] }}')" 
                                class="absolute hidden group-hover:block text-white hover:text-green-400">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                        <div class="row-title text-white font-medium truncate">{{ $song['title'] }}</div>
                        <div class="hidden sm:block truncate">{{ $song['artist'] }}</div>
                        <div class="flex items-center justify-end gap-3">
                            <!-- QUEUE BUTTON: Adds to Queue (FIFO) -->
                            <button onclick="addToQueue()" class="opacity-0 group-hover:opacity-100 hover:text-white" title="Add to Queue">
                                <i class="fa-solid fa-list-ul"></i>
                            </button>
                            <span class="font-mono text-xs">{{ $song['duration'] }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-500">
                        <p>Playlist (Circular DLL) is Empty.</p>
                        <p class="text-xs">Click "Add Node" to start.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- KOLOM 3: RIGHT SIDEBAR (QUEUE & HISTORY) -->
        <aside class="w-80 bg-[#121212] m-2 ml-0 rounded-lg p-4 flex flex-col gap-4 hidden lg:flex overflow-hidden">
            
            <!-- QUEUE SECTION (FIFO) -->
            <div class="flex-1 flex flex-col min-h-0 bg-[#181818] rounded-md p-3">
                <div class="flex justify-between items-center mb-2 border-b border-gray-700 pb-2">
                    <h3 class="font-bold text-white text-sm">Next Up (Queue)</h3>
                    <!-- Kontrol Queue -->
                    <div class="flex gap-2">
                        <!-- Clear Queue Button -->
                        <button onclick="clearQueue()" class="text-xxs bg-red-600 px-2 py-0.5 rounded text-white hover:bg-red-700 transition">
                            <i class="fa-solid fa-trash"></i> Clear
                        </button>
                        <span class="text-xxs bg-blue-600 px-2 py-0.5 rounded text-white">FIFO</span>
                    </div>
                </div>
                
                <div class="overflow-y-auto flex-1 space-y-2 pr-1">
                    @forelse ($queue as $index => $qSong)
                    <div class="flex items-center gap-3 p-2 hover:bg-[#2a2a2a] rounded group">
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-bold truncate">{{ $index + 1 }}. {{ $qSong['title'] }}</p>
                            <p class="text-gray-400 text-xs truncate">{{ $qSong['artist'] }}</p>
                        </div>
                        <!-- Tombol Naikkan/Turunkan Urutan (Reordering) -->
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                            <!-- Tombol Naik -->
                            <button onclick="moveQueueItem('{{ $index }}', 'up')" 
                                    class="text-gray-400 hover:text-white p-1 rounded-full hover:bg-[#3a3a3a]" 
                                    title="Move Up">
                                <i class="fa-solid fa-caret-up"></i>
                            </button>
                            <!-- Tombol Turun -->
                            <button onclick="moveQueueItem('{{ $index }}', 'down')" 
                                    class="text-gray-400 hover:text-white p-1 rounded-full hover:bg-[#3a3a3a]" 
                                    title="Move Down">
                                <i class="fa-solid fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 text-xs mt-4">
                        Queue is empty.<br>Hover on a song -> Click List Icon
                    </div>
                    @endforelse
                </div>
                
                <!-- Tombol Cepat Add Queue -->
                <button onclick="addToQueue()" class="mt-2 w-full py-2 bg-[#2a2a2a] hover:bg-[#3a3a3a] text-xs font-bold rounded text-white transition">
                    + Add Random to Queue
                </button>
            </div>

            <!-- HISTORY SECTION (Stack) -->
            <div class="flex-1 flex flex-col min-h-0 bg-[#181818] rounded-md p-3">
                <div class="flex justify-between items-center mb-2 border-b border-gray-700 pb-2">
                    <h3 class="font-bold text-white text-sm">Recently Played</h3>
                    <span class="text-xxs bg-red-600 px-2 py-0.5 rounded text-white">Stack (LIFO)</span>
                </div>

                <div class="overflow-y-auto flex-1 space-y-2 pr-1">
                    @forelse ($history as $hSong)
                    <div class="flex items-center gap-3 p-2 hover:bg-[#2a2a2a] rounded opacity-70 hover:opacity-100 transition">
                        <div class="text-green-500 text-xs"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-bold truncate">{{ $hSong['title'] }}</p>
                            <p class="text-gray-400 text-xs truncate">Played at {{ $hSong['played_at'] ?? 'Just now' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 text-xs mt-4">
                        No history yet.<br>Play a song to push to Stack.
                    </div>
                    @endforelse
                </div>
            </div>

        </aside>
    </div>

    <!-- PLAYER BAR (Simulasi) -->
    <div class="h-20 bg-[#181818] border-t border-[#282828] px-4 flex justify-between items-center z-50">
        <!-- Audio Player Element (Tersembunyi) -->
        <audio id="audio-player"></audio>
        
        <!-- Song Info -->
        <div class="flex items-center gap-4 w-1/3">
            <div class="w-12 h-12 bg-gray-700 flex items-center justify-center">
                <i class="fa-solid fa-music text-gray-400"></i>
            </div>
            <div id="player-song-info">
                <p class="text-white text-sm font-bold" id="current-title">Zanify Player</p>
                <p class="text-xs text-gray-400" id="current-artist">Ready to play</p>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex flex-col items-center w-1/3">
            <div class="flex items-center gap-4 text-white text-lg">
                <i class="fa-solid fa-backward-step cursor-pointer" onclick="prevSong()" title="Prev (Circular)"></i>
                <button id="player-control-button" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black hover:scale-105" onclick="togglePlayback()">
                    <i class="fa-solid fa-play text-sm ml-0.5" id="player-control-icon"></i>
                </button>
                <i class="fa-solid fa-forward-step cursor-pointer" onclick="nextSong()" title="Next (Circular/Mirip)"></i>
            </div>
            <!-- Progress Bar (Simulasi/Visualisasi) -->
             <div class="hidden sm:flex items-center gap-2 w-full text-xs text-gray-400 font-mono">
                <span id="current-time">0:00</span>
                <div class="h-1 bg-gray-600 rounded-full flex-1 group cursor-pointer relative">
                    <div id="progress-bar" class="h-1 bg-white w-0 group-hover:bg-spotify-green rounded-full relative"></div>
                    <div id="progress-knob" class="hidden w-3 h-3 bg-white rounded-full absolute top-1/2 -translate-y-1/2 shadow" style="left:0%;"></div>
                </div>
                <span id="total-duration">0:00</span>
            </div>
        </div>

        <!-- Volume -->
        <div class="w-1/3 flex justify-end gap-2 text-gray-400">
            <i class="fa-solid fa-list cursor-pointer hover:text-white" title="Queue"></i>
            <i class="fa-solid fa-volume-high"></i>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC (AJAX & Audio Control) -->
    <script>
        // DEFINISI URL SERVER (Mencegah error 'Failed to parse URL')
        const BASE_URL = 'http://127.0.0.1:8000';
        
        // STATUS PLAYER GLOBAL
        const audioPlayer = document.getElementById('audio-player');
        let currentSongId = null;
        let isPlaying = false;
        
        // =========================================================
        // A. AJAX FUNCTIONS (BACKEND)
        // =========================================================

        function addRandomSong() {
            fetch(`${BASE_URL}/playlist/add`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') location.reload();
                })
                .catch(err => console.error("Error fetching add song:", err));
        }

        function addToQueue() {
            fetch(`${BASE_URL}/playlist/queue`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') location.reload();
                })
                .catch(err => console.error("Error fetching queue:", err));
        }

        function pushToHistory() {
             fetch(`${BASE_URL}/playlist/play`)
                .then(res => res.json())
                .then(data => {
                    console.log('Lagu masuk History Stack');
                })
                .catch(err => console.error("Error pushing to history:", err));
        }
        
        // Fungsi Baru: Clear Queue
        function clearQueue() {
            // Pengganti window.confirm()
            if (!confirm('Apakah Anda yakin ingin menghapus semua lagu di Antrian?')) {
                return;
            }
            fetch(`${BASE_URL}/queue/clear`) // Endpoint baru
                .then(res => {
                    // Cek jika responsnya bukan JSON (misal 404 HTML)
                    if (!res.ok) {
                        throw new Error(`HTTP Error: ${res.status}. Pastikan rute '/queue/clear' sudah didaftarkan di routes/web.php.`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.status === 'success') location.reload();
                    else alert(data.message);
                })
                .catch(err => console.error("Error clearing queue:", err));
        }

        // Fungsi Baru: Pindahkan Urutan Lagu di Queue
        function moveQueueItem(index, direction) {
            // Implementasi nyata: Membutuhkan endpoint PHP yang mengambil index dan arah
             fetch(`${BASE_URL}/queue/move?index=${index}&direction=${direction}`)
                .then(res => {
                    // Cek jika responsnya bukan JSON (misal 404 HTML)
                    if (!res.ok) {
                        throw new Error(`HTTP Error: ${res.status}. Pastikan rute '/queue/move' sudah didaftarkan di routes/web.php.`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.status === 'success') location.reload();
                    else alert(data.message);
                })
                .catch(err => console.error("Error moving queue item:", err));
        }
        
        // =========================================================
        // B. PLAYER CONTROL FUNCTIONS (FRONTEND)
        // =========================================================
        
        /**
         * Memulai pemutaran lagu dari tabel
         * @param {string} id ID lagu
         * @param {string} url URL MP3 Azure
         * @param {string} title Judul Lagu
         * @param {string} artist Artis Lagu
         */
        function startPlayback(id, url, title, artist) {
            // 1. Jika URL kosong, tampilkan error
            if (!url || url.length < 10) {
                 // Pengganti alert()
                 const error_message = `Error: URL audio untuk "${title}" tidak valid. Cek Admin Panel.`;
                 document.getElementById('current-title').textContent = error_message;
                 document.getElementById('current-artist').textContent = "Playback failed.";
                 console.error(error_message);
                 return;
            }

            // 2. Set Audio Source dan metadata
            audioPlayer.src = url;
            currentSongId = id;
            
            document.getElementById('current-title').textContent = title;
            document.getElementById('current-artist').textContent = artist;
            
            // 3. Mainkan audio
            audioPlayer.play();
            
            // 4. Update Status UI
            updatePlaybackStatus(true);
            
            // 5. Panggil backend untuk push ke History Stack (async)
            pushToHistory();
            
            // 6. Visual update di baris tabel
            highlightPlayingRow(id);
        }

        function togglePlayback() {
            if (currentSongId === null) {
                // Jika tidak ada lagu yang dipilih, coba putar lagu pertama
                const firstSongRow = document.querySelector('.song-row');
                if (firstSongRow) {
                    startPlayback(
                        firstSongRow.dataset.id, 
                        firstSongRow.dataset.url, 
                        firstSongRow.dataset.title, 
                        firstSongRow.dataset.artist
                    );
                }
                return;
            }
            
            if (isPlaying) {
                audioPlayer.pause();
                updatePlaybackStatus(false);
            } else {
                audioPlayer.play();
                updatePlaybackStatus(true);
            }
        }
        
        // Fitur Next/Prev saat ini hanya simulasi karena harus meload seluruh CDLL di JS
        function nextSong() {
            // Pengganti alert()
            document.getElementById('current-title').textContent = "Next/Prev Song Logic (Circular DLL) - Not Implemented";
            document.getElementById('current-artist').textContent = "This requires complex frontend JS logic for CDLL.";
            console.warn('Fitur Next Song (Circular DLL + Lagu Mirip) belum diimplementasikan di Frontend JS.');
        }

        function prevSong() {
            // Pengganti alert()
            document.getElementById('current-title').textContent = "Next/Prev Song Logic (Circular DLL) - Not Implemented";
            document.getElementById('current-artist').textContent = "This requires complex frontend JS logic for CDLL.";
            console.warn('Fitur Prev Song (Circular DLL) belum diimplementasikan di Frontend JS.');
        }

        // =========================================================
        // C. UI UPDATES
        // =========================================================

        function updatePlaybackStatus(is_playing) {
            isPlaying = is_playing;
            const icon = document.getElementById('player-control-icon');
            
            if (isPlaying) {
                icon.classList.remove('fa-play');
                icon.classList.add('fa-pause');
                document.getElementById('main-play-icon').classList.remove('fa-play');
                document.getElementById('main-play-icon').classList.add('fa-pause');
            } else {
                icon.classList.remove('fa-pause');
                icon.classList.add('fa-play');
                document.getElementById('main-play-icon').classList.remove('fa-pause');
                document.getElementById('main-play-icon').classList.add('fa-play');
            }
        }
        
        function highlightPlayingRow(id) {
            document.querySelectorAll('.song-row').forEach(row => {
                row.classList.remove('playing-indicator');
                row.querySelector('.row-index').classList.remove('hidden');
                row.querySelector('.row-playing-indicator').classList.add('hidden');
            });

            const currentRow = document.querySelector(`.song-row[data-id="${id}"]`);
            if (currentRow) {
                 currentRow.querySelector('.row-index').classList.add('hidden');
                 currentRow.querySelector('.row-playing-indicator').classList.remove('hidden');
                 currentRow.classList.add('playing-indicator');
            }
        }
        
        // Event listener untuk update progress bar
        audioPlayer.addEventListener('timeupdate', () => {
            const duration = audioPlayer.duration;
            const currentTime = audioPlayer.currentTime;
            
            if (isNaN(duration)) {
                document.getElementById('total-duration').textContent = '0:00';
                return;
            }

            const percentage = (currentTime / duration) * 100;
            
            document.getElementById('progress-bar').style.width = `${percentage}%`;
            document.getElementById('progress-knob').style.left = `${percentage}%`;

            // Update waktu saat ini
            const formatTime = (time) => {
                const minutes = Math.floor(time / 60);
                const seconds = Math.floor(time % 60);
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            };

            document.getElementById('current-time').textContent = formatTime(currentTime);
            document.getElementById('total-duration').textContent = formatTime(duration);
        });
        
        // Ketika lagu selesai diputar
        audioPlayer.addEventListener('ended', () => {
            updatePlaybackStatus(false);
            // Di sini nanti bisa ditambahkan logika Next Song/Queue
        });

        // Tampilkan Knob saat mouse hover
        document.querySelector('.h-1.bg-gray-600').addEventListener('mouseenter', () => {
             document.getElementById('progress-knob').classList.remove('hidden');
        });
        document.querySelector('.h-1.bg-gray-600').addEventListener('mouseleave', () => {
             document.getElementById('progress-knob').classList.add('hidden');
        });

        // Set volume awal
        audioPlayer.volume = 0.5;

    </script>
</body>
</html>