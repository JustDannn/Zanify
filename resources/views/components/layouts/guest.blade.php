<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Zanify' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body, html {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            body::-webkit-scrollbar, html::-webkit-scrollbar {
                display: none;
            }
            .overflow-y-auto, .overflow-x-auto {
                -ms-overflow-style: none;
            }
            .overflow-y-auto::-webkit-scrollbar, .overflow-x-auto::-webkit-scrollbar {
                display: none;
            }
        </style>
    </head>
    <body>
        <main>
            {{ $slot }}
        </main>
    @livewireScripts    
    </body>
</html>
