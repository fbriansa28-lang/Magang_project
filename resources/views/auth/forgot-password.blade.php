<x-guest-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="min-h-screen flex items-center justify-center bg-[#0a0a0f] px-4 py-10 relative overflow-hidden">

        {{-- Background ambient glow --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-1/3 left-10 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-72 h-72 bg-teal-400/5 rounded-full blur-3xl"></div>
        </div>

        {{-- Decorative left-side code lines (hidden on small screens) --}}
        <div class="hidden lg:flex flex-col gap-1 absolute left-10 top-1/2 -translate-y-1/2 select-none">
            <p class="text-[10px] tracking-widest text-teal-500/20 font-mono">VERIFIED ACCESS GRANTED</p>
            <p class="text-[10px] tracking-widest text-teal-500/15 font-mono">ACCESS GRANTED VERIFIED</p>
            <p class="text-[10px] tracking-widest text-teal-500/10 font-mono">GRANTED VERIFIED ACCESS</p>
            <div class="w-px h-16 bg-teal-500/20 mt-2"></div>
        </div>

        {{-- Main card --}}
        <div class="relative w-full max-w-md rounded-xl bg-[#111118] border border-white/5 shadow-2xl shadow-black/60 overflow-hidden">

            {{-- Window title bar --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-white/5 bg-[#0d0d13]">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500/70"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/70"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500/70"></span>
                </div>
                <span class="text-[10px] tracking-[0.2em] text-gray-500 font-mono uppercase">Akses_Terbatas</span>
            </div>

            <div class="px-7 py-7">

                {{-- Back link --}}
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center w-7 h-7 mb-5 text-gray-500 hover:text-teal-400 border border-white/5 rounded-md transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m-7 7h18"/>
                    </svg>
                </a>

                {{-- Heading --}}
                <h1 class="text-xl font-bold text-white tracking-wide">LUPA SANDI</h1>
                <p class="mt-2 text-xs text-gray-500 leading-relaxed font-mono">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </p>

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-[10px] tracking-widest text-gray-500 uppercase font-mono mb-1.5">
                            Email
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9" />
                                </svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                required autofocus autocomplete="email"
                                placeholder="user@..."
                                class="w-full bg-[#1a1a22] border border-white/5 rounded-lg pl-10 pr-3 py-2.5 text-sm text-gray-200 placeholder-gray-600 font-mono focus:outline-none focus:ring-1 focus:ring-teal-500/60 focus:border-teal-500/60 transition" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-teal-500 hover:bg-teal-400 text-[#0a0a0f] font-semibold text-xs tracking-widest uppercase py-3 rounded-lg transition shadow-lg shadow-teal-500/20">
                        Kirim_Tautan_Reset
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-guest-layout>