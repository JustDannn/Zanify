@php
    // Cek apakah $cardData kosong atau tidak diset (walaupun sudah diset di class)
    if (empty($cardData)) {
        return; // Jangan render apa-apa jika data kosong
    }
@endphp

<div class="text-amber-50 bg-white/5 hover:bg-amber-50/35 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-lg shadow-lg flex items-center p-0 overflow-hidden relative transition duration-300 ease-in-out cursor-pointer group">
    
    {{-- Gunakan isset() sebelum mengakses array offset, atau gunakan operator null coalescing (??) --}}
    @if (($cardData['title'] ?? '') == 'Liked Songs')
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
            </svg>
        </div>
    @else
        {{-- Gunakan ?? 'default_value' untuk penanganan null/undefined key --}}
        <div class="w-20 h-20 object-cover" 
             style="background-image: url('{{ $cardData['image'] ?? 'https://via.placeholder.com/80' }}')" 
             alt="{{ $cardData['title'] ?? 'No Title' }}">
        </div>
    @endif
    
    <div class="flex-1 px-4 py-3">
        <span class="text-white font-bold truncate block text-wrap">{{ $cardData['title'] ?? 'No Title' }}</span>
    </div>

    @if (($cardData['has_play_button'] ?? false))
        <div class="absolute right-2 mr-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition duration-300 ease-in-out">
            <button class="bg-green-500 text-black p-3 rounded-full shadow-2xl hover:bg-green-400">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M7 6v12l10-6z"/></svg>
            </button>
        </div>
    @endif
</div>