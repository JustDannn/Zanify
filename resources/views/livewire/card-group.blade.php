<div>
    <h2 class="text-2xl font-bold text-white mb-4">Good Afternoon</h2>
    
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-4 gap-4">
        @foreach ($cards as $card)
            @livewire('cards.mini-card', ['cardData' => $card], key($card['title'])) 
        @endforeach
    </div>
</div>