<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Library CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dll-label { border-left: 5px solid #00BCD4; padding-left: 10px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-8 font-sans">

    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Admin Panel - Library</h1>
        <p class="text-gray-600 mb-6 dll-label">Struktur Data Utama: Doubly Linked List (DLL)</p>

        <!-- Pesan Sukses/Error -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Tambah Lagu (CRUD: Create) -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-blue-500"></i> Tambah Lagu Baru
            </h2>
            <form action="/admin/library/add" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf <!-- Token Keamanan Laravel -->
                <input type="text" name="title" placeholder="Judul Lagu" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <input type="text" name="artist" placeholder="Penyanyi/Artis" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <input type="text" name="album" placeholder="Album" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <select name="genre" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Genre</option>
                    <option value="Pop">Pop</option>
                    <option value="Rock">Rock</option>
                    <option value="Indie">Indie</option>
                    <option value="Jazz">Jazz</option>
                </select>
                <input type="text" name="duration" placeholder="Durasi (ex: 3:30)" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <input type="url" name="audio_url" placeholder="Azure MP3 URL Lengkap (https://...mp3)" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500 md:col-span-2">
                <button type="submit" class="bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition duration-200 col-span-1 md:col-span-4">
                    <i class="fa-solid fa-cloud-upload-alt"></i> Tambahkan ke DLL
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Lagu (CRUD: Read) -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex justify-between items-center">
                Library Lagu (Total: {{ $total }})
                <a href="/playlist" target="_blank" class="text-sm text-blue-500 hover:underline">Lihat Tampilan User <i class="fa-solid fa-external-link-alt ml-1"></i></a>
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul / Artis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Album / Genre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Audio</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($songs as $song)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ substr($song['id'], 0, 4) }}...</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $song['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $song['artist'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $song['album'] }}</div>
                                    <div class="text-xs text-blue-600 font-semibold">{{ $song['genre'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <audio controls class="w-40 h-8">
                                        <!-- Menggunakan URL yang diinput Admin -->
                                        <source src="{{ $song['audio_url'] }}" type="audio/mp3" onerror="this.parentNode.innerHTML='<span class=\'text-red-500\'>URL Error/Invalid</span>'">
                                        Browser Anda tidak mendukung elemen audio.
                                    </audio>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <!-- Tombol Delete -->
                                    <a href="#" onclick="confirmDelete('{{ $song['id'] }}', '{{ $song['title'] }}')" class="text-red-600 hover:text-red-900 ml-2 font-bold transition">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Library kosong. Tambahkan lagu pertama.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="mt-4 text-xs text-gray-500">
                CATATAN: Logika Admin Library (DLL) dan Playlist User (CDLL) saat ini belum sinkron. Implementasi sinkronisasi (pointer) akan dilakukan pada tahap selanjutnya.
            </p>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm w-full">
            <h3 class="text-lg font-bold mb-4 text-gray-800">Konfirmasi Hapus Lagu</h3>
            <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus lagu: <strong id="song-title-modal"></strong>? (Operasi pada DLL)</p>
            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('delete-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                <a id="delete-link" href="#" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold">Hapus Permanen</a>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk menampilkan modal konfirmasi hapus
        function confirmDelete(songId, songTitle) {
            document.getElementById('song-title-modal').textContent = songTitle;
            document.getElementById('delete-link').href = '/admin/library/delete/' + songId;
            document.getElementById('delete-modal').classList.remove('hidden');
            document.getElementById('delete-modal').classList.add('flex');
        }
    </script>
</body>
</html>