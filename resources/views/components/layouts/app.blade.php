<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ playerVisible: false }" @song-loaded.window="playerVisible = true">
    @livewire('components.navbar')
    <div :class="playerVisible ? 'h-[calc(100vh-64px-90px)]' : 'h-[calc(100vh-64px)]'"
        class="flex overflow-hidden transition-all duration-300">
        @unless (Route::is('admin.admin-dashboard'))
        @livewire('components.library')
        @endunless
        <main class="flex-1 overflow-y-auto bg-[#121212]" x-data="{ isSearching: false }"
            @search-active.window="isSearching = $event.detail.active">
            {{-- Search Results (shown when searching) --}}
            <div x-show="isSearching" x-cloak class="min-h-full">
                @livewire('components.search-results')
            </div>

            {{-- Main Content (hidden when searching) --}}
            <div id="main-content" x-show="!isSearching" class="min-h-full">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Music Player Bar --}}
    @livewire('components.player')

    @livewireScripts
</body>

</html>