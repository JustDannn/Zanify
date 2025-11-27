<x-layouts.app>

    {{-- NAVBAR --}}
    @livewire('components.navbar')

    {{-- WRAPPER FLEX --}}
    <div class="h-screen flex overflow-hidden">

        {{-- SIDEBAR
        @livewire('components.library') --}}

        {{-- CONTENT --}}
        <div class="flex-1">
            <div class="container mx-auto px-4 py-6">
                <h1 class="text-3xl font-bold mb-4">Welcome to Zanify</h1>
                <p class="text-gray-600">Discover and explore a world of games.</p>
            </div>
        </div>

    </div>

</x-layouts.app>

