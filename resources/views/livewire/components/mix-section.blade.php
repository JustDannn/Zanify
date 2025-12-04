<style>
    .hidden-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .hidden-scrollbar::-webkit-scrollbar {
        display: none;
    }
</style>

<div class="p-6">
    
    {{-- HEADER SECTION --}}
    <div class="flex justify-between items-end mb-6">
        <h2 class="text-2xl font-bold text-white">Made For <span class="hover:underline cursor-pointer">Dani Nurfatah</span></h2>
        <a href="#" class="text-sm font-bold text-gray-400 hover:underline">Show all</a>
    </div>

    {{-- HORIZONTAL SCROLL CONTAINER --}}
    <div class="flex space-x-6 pb-4 overflow-x-auto hidden-scrollbar">
        {{-- Loop data dan panggil komponen Card --}}
        @foreach ($mixes as $mix)
            <div class="flex-shrink-0 w-[200px]">
    @livewire('cards.card', ['cardData' => $mix], key($mix['id']))
</div>

        @endforeach
    </div>

</div>