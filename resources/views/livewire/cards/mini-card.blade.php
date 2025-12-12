@php
// Cek apakah $cardData kosong atau tidak diset (walaupun sudah diset di class)
if (empty($cardData)) {
return; // Jangan render apa-apa jika data kosong
}

$isLikedSongs = ($cardData['type'] ?? '') === 'liked' || ($cardData['title'] ?? '') == 'Liked Songs';
$isRecentlyPlayed = ($cardData['type'] ?? '') === 'playlist' && ($cardData['route'] ?? '') === 'recently-played';
$hasRoute = isset($cardData['route']);
$isArtist = ($cardData['type'] ?? '') === 'artist';
$isAlbum = ($cardData['type'] ?? '') === 'album';
$cardId = $cardData['id'] ?? null;
@endphp

@if($isLikedSongs)
<a href="{{ route('liked-songs') }}" wire:navigate @elseif($isRecentlyPlayed) <a href="{{ route('recently-played') }}" wire:navigate
    @elseif($isAlbum && $cardId) <a href="{{ route('album', $cardId) }}" wire:navigate @elseif($isArtist && $cardId) <a
    href="{{ route('artist', $cardId) }}" wire:navigate @else <div @endif
    class="text-amber-50 bg-white/5 hover:bg-amber-50/35 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-lg shadow-lg flex items-center p-0 overflow-hidden relative transition duration-300 ease-in-out cursor-pointer group">

    {{-- Image/Icon Section --}}
    @if ($isLikedSongs)
    <div class="w-20 h-20 bg-gradient-to-br from-[#450af5] to-[#c4efd9] flex items-center justify-center shrink-0">
        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
        </svg>
    </div>
    @elseif($isRecentlyPlayed)
    <div class="w-20 h-20 bg-gradient-to-br from-[#1db954] to-[#191414] flex items-center justify-center shrink-0">
        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z" />
        </svg>
    </div>
    @elseif($isArtist)
    <div class="w-20 h-20 shrink-0 overflow-hidden">
        <img src="{{ $cardData['image'] ?? 'https://via.placeholder.com/80' }}"
            alt="{{ $cardData['title'] ?? 'Artist' }}" class="w-full h-full object-cover">
    </div>
    @else
    <div class="w-20 h-20 bg-cover bg-center shrink-0"
        style="background-image: url('{{ $cardData['image'] ?? 'https://via.placeholder.com/80' }}')"
        alt="{{ $cardData['title'] ?? 'No Title' }}">
    </div>
    @endif

    {{-- Content Section --}}
    <div class="flex-1 px-4 py-3 min-w-0">
        <span class="text-white font-bold truncate block">{{ $cardData['title'] ?? 'No Title' }}</span>
        @if(isset($cardData['subtitle']))
        <span class="text-gray-400 text-sm truncate block">{{ $cardData['subtitle'] }}</span>
        @endif
    </div>

    {{-- Play Button --}}
    @if (($cardData['has_play_button'] ?? false) && !$isLikedSongs && !$isRecentlyPlayed)
    <div
        class="absolute right-2 mr-2 opacity-0 drop-shadow-xl/25 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 ease-in-out">
        <button
            class="bg-green-500 text-black p-3 rounded-full shadow-2xl hover:bg-green-400 hover:scale-105 transition">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path d="M7 6v12l10-6z" />
            </svg>
        </button>
    </div>
    @endif

    @if($isLikedSongs || $isRecentlyPlayed || ($isAlbum && $cardId) || ($isArtist && $cardId))
</a>
@else
</div>
@endif