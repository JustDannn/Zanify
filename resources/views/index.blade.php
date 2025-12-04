<x-layouts.app>
    <div class="flex-1 bg-[#121212] flex flex-col">

        <div class="container mx-auto px-4 py-6">
            <h1 class="text-white text-3xl font-bold mb-4">Welcome to Zanify</h1>
            <p class="text-white/85">Discover and explore this freaking music brow.</p>
        </div>

        <div class="sticky top-0 z-10 bg-[#121212]/80 backdrop-blur-md px-6 pt-4 pb-2">
            <div class="flex space-x-3">
                <button class="px-4 py-1.5 text-amber-50 bg-opacity-10 rounded-full text-sm">All</button>
                <button class="px-4 py-1.5 text-amber-50 bg-opacity-10 rounded-full text-sm">Music</button>
                <button class="px-4 py-1.5 text-amber-50 bg-opacity-10 rounded-full text-sm">Podcasts</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6">
            @livewire('card-group')
            @livewire('components.mix-section')
        </div>

    </div>
</x-layouts.app>
