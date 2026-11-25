<x-guest-layout>
     <x-slot name="title">LOGIN</x-slot>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="min-h-screen lg:h-screen bg-[#0a0a0f] relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute top-1/3 left-10 w-72 h-72 bg-teal-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-72 h-72 bg-teal-400/5 rounded-full blur-3xl"></div>
        </div>

        <div class="hidden lg:inline-flex absolute items-center gap-4 bg-white rounded-r-full px-7 py-3 shadow-xl">
            <div class="flex items-center gap-2">
                <img src="{{ asset('image/logo.png') }}" alt="" class="h-9 xl:h-10 w-auto object-contain">
            </div>
            <div class="w-px h-10 bg-gray-200"></div>
            <div class="flex items-center gap-2">
                <img src="{{ asset('image/logo-PG-agro-small.png') }}" alt="" class="h-10 xl:h-12 w-auto object-contain">
            </div>
        </div>

        <div class="relative min-h-screen lg:h-full grid grid-cols-1 lg:grid-cols-2">

            {{-- Kolom kiri: branding & sambutan --}}
            <div class="hidden lg:flex flex-col justify-center px-14 xl:px-16">
                <h1 class="text-3xl xl:text-4xl font-bold uppercase leading-tight tracking-tight max-w-lg">
                    <span class="block text-gray-100">Selamat Datang di</span>
                    <span class="block bg-gradient-to-r from-teal-300 to-emerald-500 bg-clip-text text-transparent">PetroCyber Challenge</span>
                </h1>

                <p class="mt-5 text-sm text-gray-400 leading-relaxed max-w-md">
                    Tempat para peserta menguji kemampuan keamanan siber dalam lingkungan yang aman, edukatif, dan kompetitif.
                    Pecahkan setiap tantangan, tingkatkan skor, dan tunjukkan kemampuan Anda dalam menghadapi berbagai skenario keamanan digital.
                </p>
            </div>

            {{-- Kolom kanan: form login --}}
            <div class="flex items-center justify-center px-4 py-6 lg:py-4">
                <div class="relative w-full max-w-sm bg-[#111118] border border-white/30 shadow-2xl shadow-black/60 overflow-hidden">

                    <div class="flex items-center justify-between px-4 py-2 border-b border-white/30 bg-[#0d0d13]">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500/70"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500/70"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-green-500/70"></span>
                        </div>
                        <span class="text-[10px] tracking-[0.2em] text-white font-mono uppercase">Akses_Terbatas</span>
                    </div>

                    <div class="px-5 py-4">

                        <h1 class="text-lg font-bold text-white tracking-wide">PETROCYBER CHALLENGE</h1>
                        <p class="mt-1 text-xs text-white leading-relaxed font-mono">
                           Inisialisasi protokol keamanan tingkat tinggi.
                        </p>

                        <div class="flex items-center gap-5 mt-3 border-b border-white/40">
                            <button type="button"
                                class="pb-2 text-[11px] font-semibold tracking-widest text-[#30A192] border-b-2 border-[#30A192] uppercase">
                                Login
                            </button>
                            <a href="{{ route('register') }}"
                                class="pb-2 text-[11px] font-semibold tracking-widest text-white hover:text-gray-300 uppercase transition">
                                Register
                            </a>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="mt-3 space-y-2.5">
                            @csrf

                            <div>
                                <label for="username" class="block text-[11px] font-medium text-white uppercase tracking-wide mb-0.5">
                                    Username
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 flex items-center text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </span>
                                    <input id="username" type="text" name="username" value="{{ old('username') }}"
                                        required autofocus autocomplete="username"
                                        placeholder="username..."
                                        class="w-full h-9 bg-[#3B494B] border border-[#262a3d] pl-9 pr-3 text-[13px] text-white font-mono outline-none focus:border-[#30A192] transition" />
                                </div>
                                <x-input-error :messages="$errors->get('username')" class="mt-1" />
                            </div>

                            <div>
                                <label for="password" class="block text-[11px] font-medium text-white uppercase tracking-wide mb-0.5">
                                    Password
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-2.5 flex items-center text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V8a4 4 0 10-8 0v3m-2 0h12a1 1 0 011 1v7a1 1 0 01-1 1H6a1 1 0 01-1-1v-7a1 1 0 011-1z" />
                                        </svg>
                                    </span>
                                    <input id="password" type="password" name="password"
                                        required autocomplete="current-password"
                                        placeholder="••••_••••"
                                        class="w-full h-9 bg-[#3B494B] border border-[#262a3d] pl-9 pr-9 text-[13px] text-white font-mono outline-none focus:border-[#30A192] transition" />
                                    <button type="button" onclick="
                                        const el = document.getElementById('password');
                                        el.type = el.type === 'password' ? 'text' : 'password';
                                    " class="absolute inset-y-0 right-3 flex items-center text-white hover:text-[#30A192] transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1" />
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <label for="remember_me" class="inline-flex items-center gap-2 text-white cursor-pointer">
                                    <input id="remember_me" type="checkbox" name="remember"
                                        class="rounded border-white/40 bg-[#3B494B] text-teal-500 focus:ring-teal-500/50 focus:ring-offset-0" />
                                    <span class="font-mono">Ingat saya</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                       class="text-white hover:text-teal-400 font-mono underline-offset-2 hover:underline transition">
                                        Lupa sandi?
                                    </a>
                                @endif
                            </div>

                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-[#30A192] hover:bg-teal-400 text-[#0a0a0f] font-semibold text-[11px] tracking-widest uppercase py-2 transition shadow-lg shadow-teal-500/20">
                                Masuk
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
