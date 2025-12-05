<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lagu - Admin Zanify</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen p-8 font-sans">

    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center gap-4">
            <a href="/admin/library" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-3xl font-extrabold text-gray-800">Edit Identitas Lagu</h1>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-lg">
            <div class="mb-6 border-b pb-4">
                <p class="text-sm text-gray-500 uppercase font-bold">File Audio (Azure)</p>
                <p class="text-blue-600 truncate font-mono text-sm mt-1">{{ basename($song['audio_url']) }}</p>
                <audio controls class="w-full mt-2 h-8">
                    <source src="{{ $song['audio_url'] }}" type="audio/mp3">
                </audio>
            </div>

            <form action="/admin/library/update/{{ $song['id'] }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Judul Lagu</label>
                    <input type="text" name="title" value="{{ $song['title'] }}" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Artis / Penyanyi</label>
                    <input type="text" name="artist" value="{{ $song['artist'] }}" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Album</label>
                    <input type="text" name="album" value="{{ $song['album'] }}" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Genre</label>
                    <select name="genre" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Pop" {{ $song['genre'] == 'Pop' ? 'selected' : '' }}>Pop</option>
                        <option value="Rock" {{ $song['genre'] == 'Rock' ? 'selected' : '' }}>Rock</option>
                        <option value="Indie" {{ $song['genre'] == 'Indie' ? 'selected' : '' }}>Indie</option>
                        <option value="Jazz" {{ $song['genre'] == 'Jazz' ? 'selected' : '' }}>Jazz</option>
                        <option value="R&B" {{ $song['genre'] == 'R&B' ? 'selected' : '' }}>R&B</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Durasi</label>
                    <input type="text" name="duration" value="{{ $song['duration'] }}" required class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2 mt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                        <i class="fa-solid fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>