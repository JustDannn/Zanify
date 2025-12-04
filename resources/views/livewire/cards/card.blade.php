@php
    // Stop rendering jika tidak ada data
    if (empty($cardData)) return;
    $image = $cardData['image'] ?? 'https://via.placeholder.com/300';
    $mixNumber = $cardData['mix_number'] ?? 'N/A';
@endphp

{{-- Kartu berukuran tetap 200px (hanya mengecil jika layar sangat kecil) --}}
<div class="w-[200px] h-full flex-shrink-0 cursor-pointer p-4 bg-white/5 hover:bg-amber-50/35 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-2xl transition duration-300 relative group">
    
    {{-- IMAGE CONTAINER --}}
    <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-lg shadow-xl">
        {{-- Main Image --}}
        <img class="w-full h-full object-cover" src="{{ $image }}">

        {{-- Play Button (Muncul saat hover) --}}
        <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 ease-in-out">
            <button class="bg-green-500 text-black p-3 rounded-full shadow-2xl hover:scale-105">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M7 6v12l10-6z"/></svg>
            </button>
        </div>

        {{-- Daily Mix Label (Di bawah Kiri) --}}
        <div class="absolute bottom-0 left-0 right-0 p-2 bg-gradient-to-t from-black/80 to-transparent">
            <span class="text-white font-extrabold text-sm block">Daily Mix</span>
        </div>

        {{-- Mix Number (Di bawah Kanan) --}}
        <div class="absolute bottom-1 right-2 p-1">
            <span class="text-white font-black text-2xl" style="text-shadow: 2px 2px 4px #000000;">{{ $mixNumber }}</span>
        </div>
    </div>

    {{-- TITLE AND DESCRIPTION --}}
    {{-- <p class="text-white font-semibold text-base truncate">{{ $title }}</p> --}}
    <p class="text-gray-400 text-sm truncate">Daily Mix</p>
</div>