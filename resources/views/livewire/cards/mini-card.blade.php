@php
// Cek apakah $cardData kosong atau tidak diset (walaupun sudah diset di class)
if (empty($cardData)) {
return; // Jangan render apa-apa jika data kosong
}

$isLikedSongs = ($cardData['title'] ?? '') == 'Liked Songs';
@endphp

@if($isLikedSongs)
<a href="{{ route('liked-songs') }}" @else <div @endif
    class="text-amber-50 bg-white/5 hover:bg-amber-50/35 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-lg shadow-lg flex items-center p-0 overflow-hidden relative transition duration-300 ease-in-out cursor-pointer group">

    {{-- Gunakan isset() sebelum mengakses array offset, atau gunakan operator null coalescing (??) --}}
    @if ($isLikedSongs)
    <div class="w-20 h-20 bg-gradient-to-br from-[#450af5] to-[#c4efd9] flex items-center justify-center shrink-0">
        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
        </svg>
    </div>
    @else
    {{-- Gunakan ?? 'default_value' untuk penanganan null/undefined key --}}
    <div class="w-20 h-20 bg-cover bg-center shrink-0"
        style="background-image: url('{{ $cardData['image'] ?? 'https://via.placeholder.com/80' }}')"
        alt="{{ $cardData['title'] ?? 'No Title' }}">
    </div>
    @endif

    <div class="flex-1 px-4 py-3 min-w-0">
        <span class="text-white font-bold truncate block">{{ $cardData['title'] ?? 'No Title' }}</span>
    </div>

    @if (($cardData['has_play_button'] ?? false) && !$isLikedSongs)
    <div
        class="absolute right-2 mr-2 opacity-0 drop-shadow-xl/25 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 ease-in-out">
        <button class="bg-green-500 text-black p-3 rounded-full shadow-2xl hover:bg-green-400 ">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path d="M7 6v12l10-6z" />
            </svg>
        </button>
    </div>
    @endif

    @if($isLikedSongs)
</a>
@else
</div>
@endif