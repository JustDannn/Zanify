<x-layouts.app>

    {{-- CONTENT - No extra wrapper needed, app.blade.php handles scrolling --}}
    <div class="bg-[#121212]">
        {{-- HEADER DENGAN BUTTON ALL/MUSIC/PODCASTS --}}
        <div class="sticky top-0 backdrop-blur-sm z-10 px-6 pt-4 pb-2">
            <div class="flex space-x-3">
                <button
                    class="px-4 py-1.5 text-amber-50 bg-white/20 bg-clip-padding backdrop-filter backdrop-blur-sm rounded-full text-sm font-medium">All</button>
                <button
                    class="px-4 py-1.5 text-amber-50 bg-white/10 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-full text-sm hover:bg-amber-50/35 transition">Music</button>
                <button
                    class="px-4 py-1.5 text-amber-50 bg-white/10 bg-clip-padding backdrop-filter backdrop-blur-sm bg-opacity-10 rounded-full text-sm hover:bg-amber-50/35 transition">Podcasts</button>
            </div>
        </div>

        {{-- Quick Access Cards (Greeting + Recently Played/Liked) --}}
        @livewire('card-group')

        {{-- Daily Mixes (Made For You) --}}
        @livewire('components.mix-section')

        {{-- Dynamic Sections (Popular, New Releases, Artists, Albums) --}}
        @livewire('components.home-sections')
    </div>

</x-layouts.app>