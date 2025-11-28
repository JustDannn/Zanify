<x-layouts.app>

    {{-- NAVBAR --}}
    @livewire('components.navbar')

    {{-- WRAPPER FLEX --}}
    <div class="h-screen flex overflow-hidden">

        {{-- SIDEBAR
        @livewire('components.library') --}}

        {{-- CONTENT --}}
        <div class="flex-1 overflow-y-auto bg-[#121212] ">
            <div class="container mx-auto px-4 py-6">
                <h1 class="text-white text-3xl font-bold mb-4">Welcome to Zanify</h1>
                <p class="text-white/85">Discover and explore this freaking music brow.</p>
            </div>
            {{-- HEADER DENGAN BUTTON ALL/MUSIC/PODCASTS --}}
            <div class="sticky top-0 backdrop-blur-sm z-10 px-6 pt-4 pb-2">
                <div class="flex space-x-3">
                    <button class="px-4 py-1.5 text-amber-50 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-full text-sm">All</button>
                    <button class="px-4 py-1.5 text-amber-50 bg-white/10 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-full text-sm hover:bg-amber-50/35">Music</button>
                    <button class="px-4 py-1.5 text-amber-50 bg-white/10 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-full text-sm hover:bg-amber-50/35">Podcasts</button>
                </div>
            </div>
            @livewire('card-group')
            @livewire('components.mix-section') 
        </div>
    </div>

</x-layouts.app>

