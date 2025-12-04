<x-layouts.guest>
<div class="min-h-screen flex items-center justify-center bg-[#121212]">
    <div class="bg-[#181818] p-8 rounded-2xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-500 mb-2">Zanify</h1>
            <p class="text-gray-400">Your Music, Your Way</p>
        </div>

        <h2 class="text-2xl text-white font-bold mb-6">Login</h2>

        <form wire:submit.prevent="login" class="space-y-4">

            <div>
                <label class="text-white text-sm font-medium">Email</label>
                <input type="email" wire:model="email"
                       class="w-full mt-1 px-3 py-2 rounded bg-[#282828] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('email') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-white text-sm font-medium">Password</label>
                <input type="password" wire:model="password"
                       class="w-full mt-1 px-3 py-2 rounded bg-[#282828] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('password') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <button
                class="w-full bg-green-500 hover:bg-green-600 text-black font-bold py-2 rounded-lg transition duration-200">
                Login
            </button>

        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">Don't have an account? 
                <a href="{{ route('register') }}" class="text-green-500 hover:text-green-400 font-semibold">Sign up</a>
            </p>
        </div>
    </div>
</div>
</x-layouts.guest>
