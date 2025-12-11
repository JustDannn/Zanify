<div class="p-6">

    {{-- HEADER SECTION --}}
    <div class="flex justify-between items-end mb-6">
        <h2 class="text-2xl font-bold text-white">{{ $sectionTitle }}</h2>
        @if(count($mixes) > 4)
        <a href="#" class="text-sm font-bold text-gray-400 hover:underline">Show all</a>
        @endif
    </div>

    @if(count($mixes) > 0)
    {{-- HORIZONTAL SCROLL CONTAINER --}}
    <div class="flex space-x-6 pb-4 overflow-x-auto scrollbar-hide">
        {{-- Loop data dan panggil komponen Card --}}
        @foreach ($mixes as $mix)
        @livewire('cards.card', ['cardData' => $mix], key('mix-'.$mix['id']))
        @endforeach
    </div>
    @else
    <p class="text-gray-400">No mixes available yet. Add some songs to get started!</p>
    @endif

</div>