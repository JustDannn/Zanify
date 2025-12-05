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
        <!-- HEADER: JUDUL & TOMBOL SYNC -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Admin Panel - Library</h1>
                <p class="text-gray-600 dll-label">Struktur Data Utama: Doubly Linked List (DLL)</p>
            </div>
            
            <!-- TOMBOL SYNC DARI AZURE (FITUR PENTING) -->
            <a href="/admin/library/sync" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center gap-2 transition transform hover:scale-105">
                <i class="fa-solid fa-cloud-arrow-down"></i> Sync dari Azure
            </a>
        </div>

        <!-- NOTIFIKASI -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 flex items-center gap-2" role="alert">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <!-- FORM TAMBAH LAGU -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-music text-blue-500"></i> Kelola Lagu Manual / Upload
            </h2>
            
            <form action="/admin/library/add" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf 
                <input type="text" name="title" placeholder="Judul Lagu" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <input type="text" name="artist" placeholder="Penyanyi/Artis" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <input type="text" name="album" placeholder="Album" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <select name="genre" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">Pilih Genre</option>
                    <option value="Pop">Pop</option>
                    <option value="Rock">Rock</option>
                    <option value="Indie">Indie</option>
                    <option value="Jazz">Jazz</option>
                    <option value="R&B">R&B</option>
                </select>
                <input type="text" name="duration" placeholder="Durasi (ex: 3:30)" required class="p-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                
                <!-- PILIHAN INPUT: UPLOAD atau MANUAL -->
                <div class="md:col-span-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="input_type" value="upload" checked onclick="toggleInput('upload')" class="text-blue-600 focus:ring-blue-500">
                            <span class="font-bold text-sm text-gray-700">Upload File Baru</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="input_type" value="manual" onclick="toggleInput('manual')" class="text-blue-600 focus:ring-blue-500">
                            <span class="font-bold text-sm text-gray-700">Pilih File Azure Lama</span>
                        </label>
                    </div>

                    <!-- Input Upload -->
                    <div id="upload-input">
                        <input type="file" name="audio_file" accept=".mp3,audio/mpeg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-300 rounded-lg p-1 bg-white">
                        <p class="text-xs text-gray-500 mt-1">File akan diupload ke Azure Container: <strong>{{ env('AZURE_CONTAINER_NAME') }}</strong></p>
                    </div>

                    <!-- Input Manual (Nama File Saja) -->
                    <div id="manual-input" class="hidden">
                        <input type="text" name="audio_filename" placeholder="Contoh: Barasuara - Terbuang Dalam Waktu.mp3" class="w-full p-2 border rounded text-sm font-mono">
                        <p class="text-xs text-gray-500 mt-1">Masukkan nama file persis seperti di Azure Portal (Case Sensitive).</p>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition duration-200 col-span-1 md:col-span-4 flex justify-center items-center gap-2 shadow-md">
                    <i class="fa-solid fa-save"></i> Simpan ke Library
                </button>
            </form>
        </div>

        <!-- TABEL DAFTAR LAGU -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex justify-between items-center">
                Library Lagu (Total: {{ $total }})
                <a href="/playlist" target="_blank" class="text-sm text-blue-500 hover:underline flex items-center gap-1">
                    Lihat Tampilan User <i class="fa-solid fa-external-link-alt"></i>
                </a>
            </h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Lagu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Audio Player</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($songs as $song)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $song['title'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $song['artist'] }} • {{ $song['genre'] }}</div>
                                    <div class="text-xs text-blue-400 truncate max-w-xs mt-1 font-mono" title="{{ $song['audio_url'] }}">
                                        {{ basename($song['audio_url']) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <audio controls class="w-64 h-8" preload="none">
                                        <source src="{{ $song['audio_url'] }}" type="audio/mp3">
                                        Browser Anda tidak mendukung elemen audio.
                                    </audio>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <!-- TOMBOL EDIT (PENSIL) -->
                                    <a href="/admin/library/edit/{{ $song['id'] }}" class="text-yellow-600 hover:text-yellow-900 ml-2 font-bold inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-yellow-100 transition" title="Edit Lagu">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    
                                    <!-- TOMBOL HAPUS (SAMPAH) -->
                                    <a href="#" onclick="confirmDelete('{{ $song['id'] }}', '{{ $song['title'] }}')" class="text-red-600 hover:text-red-900 ml-2 font-bold inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-red-100 transition" title="Hapus Lagu">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-500 flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-music text-4xl text-gray-300 mb-2"></i>
                                    <p>Library kosong.</p>
                                    <p class="text-sm">Silakan Upload Lagu atau klik tombol <strong>Sync dari Azure</strong>.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm w-full transform transition-all scale-100">
            <h3 class="text-lg font-bold mb-4 text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-red-500"></i> Hapus Lagu?
            </h3>
            <p class="text-gray-600 mb-6">Lagu <strong id="song-title-modal" class="text-gray-800"></strong> akan dihapus permanen dari Library dan Playlist User.</p>
            <div class="flex justify-end gap-3">
                <button onclick="document.getElementById('delete-modal').classList.add('hidden'); document.getElementById('delete-modal').classList.remove('flex');" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-medium transition">Batal</button>
                <a id="delete-link" href="#" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold shadow-md transition">Ya, Hapus</a>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(songId, songTitle) {
            document.getElementById('song-title-modal').textContent = songTitle;
            document.getElementById('delete-link').href = '/admin/library/delete/' + songId;
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function toggleInput(type) {
            if (type === 'upload') {
                document.getElementById('upload-input').classList.remove('hidden');
                document.getElementById('manual-input').classList.add('hidden');
            } else {
                document.getElementById('upload-input').classList.add('hidden');
                document.getElementById('manual-input').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>