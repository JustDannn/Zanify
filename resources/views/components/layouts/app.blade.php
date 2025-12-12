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

<body x-data="{ 
    playerVisible: false,
    toast: { show: false, message: '' }
}" @song-loaded.window="playerVisible = true" @notify.window="
    toast.message = $event.detail.message;
    toast.show = true;
    setTimeout(() => toast.show = false, 3000);
">
    {{-- Toast Notification --}}
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4" x-cloak
        class="fixed bottom-28 left-1/2 -translate-x-1/2 z-100 bg-[#3b82f6] text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3">
        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
        </svg>
        <span x-text="toast.message" class="font-medium"></span>
    </div>

    @livewire('components.navbar')
    <div :class="playerVisible ? 'h-[calc(100vh-64px-90px)]' : 'h-[calc(100vh-64px)]'"
        class="flex overflow-hidden transition-all duration-300">
        @unless (Route::is('admin.admin-dashboard'))
        @livewire('components.library')
        @endunless
        <main class="flex-1 overflow-y-auto bg-[#121212] scrollbar-hide">
            {{-- Main Content --}}
            <div id="main-content" class="min-h-full">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Music Player Bar --}}
    @persist('player')
    @livewire('components.player')
    @endpersist

    {{-- Queue Sidebar --}}
    @persist('queue')
    @livewire('components.queue')
    @endpersist

    @livewireScripts
</body>

</html>