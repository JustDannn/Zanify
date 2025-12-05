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
                    <span class="font-bold">Search</span>
                </div>
            </div>
            <div class="bg-[#121212] rounded-lg p-4 flex-1 flex flex-col">
                <div class="flex justify-between items-center mb-4 text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-lines-leaning text-xl"></i>
                        <span class="font-bold">Library</span>
                    </div>
                </div>
                <!-- Indikator Struktur Data -->
                <div class="space-y-2">
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-green-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">Playlist</h4>
                        <p class="text-white font-bold">Linked List</p>
                    </div>
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-blue-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">Queue (Next Up)</h4>
                        <p class="text-white font-bold">Queue (FIFO)</p>
                    </div>
                    <div class="p-3 bg-[#2a2a2a] rounded-md border-l-4 border-red-500">
                        <h4 class="font-bold text-xs text-gray-400 uppercase">History</h4>
                        <p class="text-white font-bold">Stack (LIFO)</p>
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
                        <i class="fa-solid fa-link text-6xl text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-white">Main Playlist</p>
                        <h1 class="text-5xl font-black mb-4 tracking-tighter">Linked List Mix</h1>
                        <p class="text-gray-300 text-sm font-medium">Lagu di sini disimpan menggunakan <strong>Singly Linked List</strong>.</p>
                        <div class="flex items-center gap-2 text-sm font-bold text-white mt-2">
                            <span>Total Nodes: {{ $total }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="px-6 bg-[#121212]/30 backdrop-blur-sm sticky top-0 z-10 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button class="w-12 h-12 bg-spotify-green rounded-full flex items-center justify-center text-black hover:scale-105 shadow-lg">
                        <i class="fa-solid fa-play text-lg pl-1"></i>
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
                    <div class="group grid grid-cols-[auto_1fr_1fr_auto] gap-4 px-4 py-2 hover:bg-[#2a2a2a] rounded-md items-center text-sm text-gray-400 transition">
                        <div class="w-6 text-center relative h-6 flex items-center justify-center">
                            <span class="group-hover:hidden text-xs">{{ $index + 1 }}</span>
                            <!-- PLAY BUTTON: Moves to History (Stack) -->
                            <button onclick="playSong()" class="absolute hidden group-hover:block text-white hover:text-green-400">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                        <div class="text-white font-medium truncate">{{ $song['title'] }}</div>
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
                        <p>Linked List is Empty.</p>
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
                    <span class="text-xxs bg-blue-600 px-2 py-0.5 rounded text-white">FIFO</span>
                </div>
                
                <div class="overflow-y-auto flex-1 space-y-2 pr-1">
                    @forelse ($queue as $qSong)
                    <div class="flex items-center gap-3 p-2 hover:bg-[#2a2a2a] rounded group">
                        <div class="text-gray-400 text-xs"><i class="fa-solid fa-music"></i></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-bold truncate">{{ $qSong['title'] }}</p>
                            <p class="text-gray-400 text-xs truncate">{{ $qSong['artist'] }}</p>
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
        <div class="flex items-center gap-4 w-1/3">
            <div class="w-12 h-12 bg-gray-700 flex items-center justify-center">
                <i class="fa-solid fa-music text-gray-400"></i>
            </div>
            <div>
                <p class="text-white text-sm font-bold">Zanify Player</p>
                <p class="text-xs text-gray-400">Ready to play</p>
            </div>
        </div>
        <div class="flex flex-col items-center w-1/3">
            <div class="flex items-center gap-4 text-white text-lg">
                <i class="fa-solid fa-backward-step cursor-pointer"></i>
                <button onclick="playSong()" class="w-8 h-8 bg-white rounded-full flex items-center justify-center text-black hover:scale-105">
                    <i class="fa-solid fa-play text-sm ml-0.5"></i>
                </button>
                <i class="fa-solid fa-forward-step cursor-pointer"></i>
            </div>
        </div>
        <div class="w-1/3 flex justify-end gap-2 text-gray-400">
            <i class="fa-solid fa-list cursor-pointer hover:text-white" title="Queue"></i>
            <i class="fa-solid fa-volume-high"></i>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC (AJAX) -->
    <script>
        // DEFINISI URL SERVER (Mencegah error 'Failed to parse URL')
        const BASE_URL = 'http://127.0.0.1:8000';

        // Helper: Tampilkan Loading di Button
        function setLoading(btn, text) {
            const original = btn.innerHTML;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i>`;
            btn.disabled = true;
            return function reset() {
                btn.innerHTML = original;
                btn.disabled = false;
            };
        }

        // 1. TAMBAH KE LINKED LIST
        function addRandomSong() {
            // Menggunakan BASE_URL agar alamatnya absolut
            fetch(`${BASE_URL}/playlist/add`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') location.reload();
                })
                .catch(err => console.error("Error fetching add song:", err));
        }

        // 2. TAMBAH KE QUEUE (Antrian)
        function addToQueue() {
            fetch(`${BASE_URL}/playlist/queue`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') location.reload();
                })
                .catch(err => console.error("Error fetching queue:", err));
        }

        // 3. PUTAR LAGU (Masuk Stack History)
        function playSong() {
            fetch(`${BASE_URL}/playlist/play`)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') location.reload();
                })
                .catch(err => console.error("Error fetching play song:", err));
        }
    </script>
</body>
</html>