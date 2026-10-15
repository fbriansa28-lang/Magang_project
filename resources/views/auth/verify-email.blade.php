<x-guest-layout>

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

                {{-- Icon --}}
                <div class="inline-flex items-center justify-center w-10 h-10 mb-5 rounded-lg bg-teal-500/10 border border-teal-500/20 text-teal-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>

                {{-- Heading --}}
                <h1 class="text-xl font-bold text-white tracking-wide">VERIFIKASI EMAIL</h1>
                <p class="mt-2 text-xs text-gray-500 leading-relaxed font-mono">
                    Inisialisasi protokol keamanan tingkat tinggi.
                </p>

                {{-- Message --}}
                <div class="mt-6 text-xs text-gray-400 leading-relaxed font-mono">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mt-4 px-3 py-2.5 rounded-lg bg-teal-500/10 border border-teal-500/20 text-[11px] text-teal-400 font-mono leading-relaxed">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif

                {{-- Actions --}}
                <div class="mt-6 flex items-center justify-between gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center justify-center gap-2 bg-teal-500 hover:bg-teal-400 text-[#0a0a0f] font-semibold text-xs tracking-widest uppercase py-3 px-4 rounded-lg transition shadow-lg shadow-teal-500/20">
                            Kirim_Ulang
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-xs font-mono text-gray-500 hover:text-teal-400 underline-offset-2 hover:underline transition">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</x-guest-layout>