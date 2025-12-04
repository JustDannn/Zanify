<x-layouts.guest>
<div class="min-h-screen flex items-center justify-center bg-[#121212]">
    <div class="bg-[#181818] p-8 rounded-2xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-green-500 mb-2">Zanify</h1>
            <p class="text-gray-400">Your Music, Your Way</p>
        </div>

        <h2 class="text-2xl text-white font-bold mb-6">Create Account</h2>

        <form wire:submit.prevent="register" class="space-y-4">

            <div>
                <label class="text-white text-sm font-medium">Full Name</label>
                <input type="text" wire:model="name" placeholder="John Doe"
                       class="w-full mt-1 px-3 py-2 rounded bg-[#282828] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('name') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-white text-sm font-medium">Email</label>
                <input type="email" wire:model="email" placeholder="you@example.com"
                       class="w-full mt-1 px-3 py-2 rounded bg-[#282828] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('email') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-white text-sm font-medium">Password</label>
                <input type="password" wire:model="password" placeholder="At least 8 characters"
                       class="w-full mt-1 px-3 py-2 rounded bg-[#282828] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500">
                @error('password') <span class="text-red-400 text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-white text-sm font-medium">Confirm Password</label>
                <input type="password" wire:model="password_confirmation" placeholder="Repeat password"
                       class="w-full mt-1 px-3 py-2 rounded bg-[#282828] text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <button
                class="w-full bg-green-500 hover:bg-green-600 text-black font-bold py-2 rounded-lg transition duration-200">
                Sign Up
            </button>

        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">Already have an account? 
                <a href="{{ route('login') }}" class="text-green-500 hover:text-green-400 font-semibold">Log in</a>
            </p>
        </div>
    </div>
</div>
</x-layouts.guest>
