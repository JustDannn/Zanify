@php
// Stop rendering jika tidak ada data
if (empty($cardData)) return;
$image = $cardData['image'] ?? 'https://via.placeholder.com/300';
$mixNumber = $cardData['mix_number'] ?? 'N/A';
@endphp

{{-- Kartu berukuran tetap 200px (hanya mengecil jika layar sangat kecil) --}}
<div
    class="w-[200px] h-full shrink-0 cursor-pointer p-4 bg-white/5 hover:bg-amber-50/35 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-2xl transition duration-300 relative group">

    {{-- IMAGE CONTAINER --}}
    <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-lg shadow-xl">
        {{-- Main Image --}}
        <img class="w-full h-full object-cover" src="{{ $image }}">

        {{-- Mix Number (Di belakang, besar) --}}
        <div class="absolute bottom-1 right-2 z-0">
            <span class="text-white/90 font-black text-5xl leading-none"
                style="text-shadow: 2px 2px 8px rgba(0,0,0,0.8);">{{ $mixNumber }}</span>
        </div>

        {{-- Daily Mix Label --}}
        <div class="absolute bottom-0 left-0 right-0 p-3 bg-gradient-to-t from-black/80 to-transparent z-10">
            <span class="text-white font-extrabold text-sm">Daily Mix</span>
        </div>

        {{-- Play Button (Muncul saat hover, di atas semua) --}}
        <div
            class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 ease-in-out z-20">
            <button class="bg-green-500 text-black p-3 rounded-full shadow-2xl hover:scale-105">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                    <path d="M7 6v12l10-6z" />
                </svg>
            </button>
        </div>
    </div>

    {{-- TITLE AND DESCRIPTION --}}
    {{-- <p class="text-white font-semibold text-base truncate">{{ $title }}</p> --}}
    <p class="text-gray-400 text-sm truncate">Daily Mix</p>
</div>