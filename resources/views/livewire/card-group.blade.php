<div class="p-6">
    <h2 class="text-2xl font-bold text-white mb-4">{{ $greeting }}</h2>

    @if(count($cards) > 0)
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-4 gap-4">
        @foreach ($cards as $index => $card)
        @livewire('cards.mini-card', ['cardData' => $card], key('card-'.$index.'-'.($card['id'] ?? $card['title'])))
        @endforeach
    </div>
    @else
    <p class="text-gray-400">Start listening to see your personalized content here!</p>
    @endif
</div>