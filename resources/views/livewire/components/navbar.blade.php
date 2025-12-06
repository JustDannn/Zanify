<div class="sticky top-0 z-50">
    <div x-data="{ open: false }" @click.outside="open = false"
        class="w-full bg-black text-white px-4 py-3 flex items-center gap-3 relative justify-between">

        {{-- LEFT SECTION --}}
        <div class="flex items-center gap-3 flex-1">

            {{-- Icon Home --}}
            <button class="p-2 rounded-full hover:bg-gray-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </button>

            {{-- SEARCH --}}
            <div class="relative flex-1 max-w-lg">
                <div class="flex items-center bg-[#161616] rounded-full px-4 py-2">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>

                    <input wire:model.live="query" @focus="open = true" id="global-search" type="text"
                        placeholder="What do you want to play?"
                        class="bg-transparent w-full focus:outline-none text-gray-200" />
                </div>

                {{-- DROPDOWN --}}
                @if(!empty($suggestions))
                <div
                    class="absolute mt-2 w-full bg-[#1c1c1c] rounded-xl shadow-xl border border-gray-800 animate-fadeIn z-50">
                    @foreach($suggestions as $item)
                    <div wire:click="selectSuggestion('{{ $item }}')"
                        class="px-4 py-2 hover:bg-gray-800 cursor-pointer rounded-lg transition">
                        {{ $item }}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Icon Recent --}}
            <button class="p-2 rounded-full hover:bg-gray-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        </div>

        {{-- RIGHT SECTION --}}
        <div class="flex items-center gap-4">

            {{-- NOTIFICATION --}}
            <button class="relative p-2 rounded-full hover:bg-gray-800 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                {{-- RED DOT --}}
                <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            {{-- PROFILE --}}
            <div x-data="{ open: false }" class="relative">
                {{-- PROFILE BUTTON --}}
                <button @click="open = !open"
                    class="flex items-center gap-2 hover:bg-gray-800 p-2 rounded-full transition">
                    <img src="https://api.dicebear.com/9.x/notionists-neutral/svg?seed={{ auth()->user()->name ?? 'Guest' }}"
                        alt="User Avatar" class="w-8 h-8 rounded-full object-cover bg-gray-700" />
                </button>

                {{-- DROPDOWN --}}
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-3 w-40 bg-[#1c1c1c] rounded-xl border border-gray-800 shadow-xl z-50">
                    <a class="block px-4 py-2 hover:bg-gray-800 transition">Profile</a>
                    <a class="block px-4 py-2 hover:bg-gray-800 transition">Settings</a>
                    @if(session('is_admin'))
                    <a href="{{ route('admin.admin-dashboard') }}" class="block px-4 py-2 hover:bg-gray-800 transition">
                        Admin Dashboard
                    </a>
                    @endif
                    <livewire:auth.logout />

                </div>
            </div>


        </div>

    </div>

    {{-- ANIMATION --}}
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.15s ease-out;
        }
    </style>

    {{-- CTRL + K --}}
    <script>
        document.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.querySelector('#global-search').focus();
            }
        });
    </script>
</div>