<div class="min-h-screen bg-black flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-neutral-900 p-10 rounded-2xl shadow-xl text-white">

        {{-- Logo --}}
        <div class="flex justify-center mb-6">
            <img src="/spotify.svg" class="w-10" alt="Logo">
        </div>

        <h1 class="text-3xl font-bold text-center mb-8">
            Log in to continue
        </h1>

        <form wire:submit.prevent="login" class="space-y-4">

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
                    placeholder="Your password">
                @error('password') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- Login button --}}
            <button class="w-full bg-green-500 hover:bg-green-600 text-black font-semibold p-3 rounded-full">
                Log In
            </button>

        </form>
        <p class="text-center text-neutral-400 mt-8">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-white font-semibold hover:underline">
                Sign Up
            </a>
        </p>

    </div>
</div>