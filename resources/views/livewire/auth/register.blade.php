<div class="min-h-screen bg-black flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-neutral-900 p-10 rounded-2xl shadow-xl text-white">

        {{-- Logo --}}
        <div class="flex justify-center mb-6">
            <img src="/spotify.svg" class="w-10" alt="Logo">
        </div>

        <h1 class="text-3xl font-bold text-center mb-8">
            Sign Up for Free
        </h1>

        <form wire:submit.prevent="register" class="space-y-4">

            {{-- Name --}}
            <div>
                <label class="text-sm mb-1 block">Full Name</label>
                <input type="text" wire:model="name"
                    class="w-full p-3 rounded-lg bg-neutral-800 border border-neutral-700 focus:border-green-500 focus:ring-0"
                    placeholder="Your name">
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="text-sm mb-1 block">Email address</label>
                <input type="email" wire:model="email"
                    class="w-full p-3 rounded-lg bg-neutral-800 border border-neutral-700 focus:border-green-500 focus:ring-0"
                    placeholder="name@domain.com">
                @error('email') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="text-sm mb-1 block">Password</label>
                <input type="password" wire:model="password"
                    class="w-full p-3 rounded-lg bg-neutral-800 border border-neutral-700 focus:border-green-500 focus:ring-0"
                    placeholder="At least 6 characters">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- Button --}}
            <button class="w-full bg-green-500 hover:bg-green-600 text-black font-semibold p-3 rounded-full">
                Sign Up
            </button>

        </form>

        {{-- Divider --}}
        <div class="flex items-center my-6">
            <div class="flex-1 h-px bg-neutral-700"></div>
            <span class="px-3 text-neutral-400">or</span>
            <div class="flex-1 h-px bg-neutral-700"></div>
        </div>

        {{-- Login Redirect --}}
        <p class="text-center text-neutral-400 mt-8">
            Already have an account?
            <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">
                Log In
            </a>
        </p>

    </div>
</div>