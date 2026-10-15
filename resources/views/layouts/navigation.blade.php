<aside class="bg-[#161822] light:bg-[#F4F4F5] border-r border-[#1e2130] light:border-[#999999] flex flex-col h-full overflow-hidden transition-colors">

    <a href="{{ route('dashboard') }}" class="block px-5 pt-6 pb-5 shrink-0">
        <div class="text-[25px] font-extrabold text-slate-50 light:text-[#003033] leading-[1.2] tracking-tight">PETROCYBER</div>
        <div class="text-[25px] font-extrabold text-slate-50 light:text-[#003033] leading-[1.2] tracking-tight">CHALLENGE</div>
    </a>

    <nav class="flex-1 py-4 px-3 flex flex-col gap-1.5 overflow-y-auto min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

        {{-- BERANDA --}}
        <a href="{{ route('dashboard') }}"
           class="relative overflow-hidden flex shrink-0 items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
           {{ request()->routeIs('dashboard')
                ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2"/>
                <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
                <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2"/>
            </svg>
            <span>BERANDA</span>

            <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ request()->routeIs('dashboard') ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
        </a>

        {{-- KELOLA PENGGUNA --}}
        @can('kelola managemen user')
        @php
            $userMenuActive = request()->routeIs('users.*')
                        || request()->routeIs('permissions.*')
                        || request()->routeIs('roles.*');

            $permActive  = request()->routeIs('permissions.*');
            $roleActive  = request()->routeIs('roles.*');
            $usersActive = request()->routeIs('users.*');
        @endphp

        <div x-data="{ open: {{ $userMenuActive ? 'true' : 'false' }} }">

            <button @click="open = !open"
                    class="relative overflow-hidden w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
                    {{ $userMenuActive
                        ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>KELOLA PENGGUNA</span>

                <svg class="w-[13px] h-[13px] shrink-0 transition-transform ml-auto" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10l3 3 3-3"/>
                </svg>
                <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ $userMenuActive ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
            </button>

            <div x-show="open" x-transition
                 class="mt-1 ml-4 shrink-0 border-l border-[#273244] pl-3 light:border-[#1a2e28] flex flex-col gap-0.5">

                @can('view permission')
                <a href="{{ route('permissions.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $permActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span> KELOLA IZIN</span>
                </a>
                @endcan

                @can('view role')
                <a href="{{ route('roles.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $roleActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span>KELOLA PERAN</span>
                </a>
                @endcan

                @can('view user')
                <a href="{{ route('users.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $usersActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span>DATA PENGGUNA</span>
                </a>
                @endcan

            </div>
        </div>
        @endcan

        {{-- PAPAN PERINGKAT --}}
        @can('view leaderboard')
        <a href="{{ route('leaderboard.index') }}"
           class="relative overflow-hidden flex shrink-0 items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
           {{ request()->routeIs('leaderboard.*')
                ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V10m6 9V5m6 14v-7"/>
            </svg>
            <span>PAPAN PERINGKAT</span>

            <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ request()->routeIs('leaderboard.*') ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
        </a>
        @endcan

        {{-- KELOLA KONTEN --}}
        @can('view managemen konten')
        @php
            $challengeMenuActive = request()->routeIs('kategori.*')
                        || request()->routeIs('challenges.*')
                        || request()->routeIs('bank-soal.*')
                        ||request()->routeIs('learning-materials.*');

            $catActive   = request()->routeIs('kategori.*');
            $challActive = request()->routeIs('challenges.*');
            $bankActive  = request()->routeIs('bank-soal.*');
            $learActive = request()->routeIs('learning-materials.*');
        @endphp

        <div x-data="{ open: {{ $challengeMenuActive ? 'true' : 'false' }} }">

            <button @click="open = !open"
                    class="relative overflow-hidden w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
                    {{ $challengeMenuActive
                        ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.355-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span>KELOLA KONTEN</span>

                <svg class="w-[13px] h-[13px] shrink-0 transition-transform ml-auto" :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10l3 3 3-3"/>
                </svg>
                <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ $challengeMenuActive ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
            </button>

            <div x-show="open" x-transition
                 class="mt-1 ml-4 shrink-0 border-l border-[#273244] pl-3 light:border-[#1a2e28] flex flex-col gap-0.5">

                @can('view kategori')
                <a href="{{ route('kategori.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $catActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span>KELOLA KATEGORI</span>
                </a>
                @endcan

                @can('view kelola learning')
                <a href="{{ route('learning-materials.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $learActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span>KELOLA BELAJAR</span>
                </a>
                @endcan

                @can('view bank soal')
                <a href="{{ route('bank-soal.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $bankActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span>BANK SOAL</span>
                </a>
                @endcan

                @can('view challenge')
                <a href="{{ route('challenges.index') }}"
                   class="flex shrink-0 items-center gap-2 px-2.5 py-1.5 rounded-md text-[11.5px] font-medium tracking-wide transition
{{ $challActive
                         ? 'bg-gray-400/15 border-r-4 border-[#30A192] light:border-[#003033] text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                        : 'text-gray-500 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
                    <span class="w-[5px] h-[5px] rounded-full bg-current opacity-60 shrink-0"></span>
                    <span>KELOLA TANTANGAN</span>
                </a>
                @endcan

            </div>
        </div>
        @endcan

        @can('view learning')
        <a href="{{ route('learning.index') }}"
           class="relative overflow-hidden flex shrink-0 items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
           {{ request()->routeIs('learning.*')
                ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
            </svg>
            <span>BELAJAR &amp; TANTANGAN</span>

            <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ request()->routeIs('learning.*') ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
        </a>
        @endcan

        {{-- KARTU HASIL --}}
        @can('view rapot')
        <a href="{{ route('rapot.index') }}"
           class="relative overflow-hidden flex shrink-0 items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
           {{ request()->routeIs('rapot.*')
                ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>KARTU HASIL</span>

            <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ request()->routeIs('rapot.*') ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
        </a>
        @endcan

        @role('Peserta')
        <a href="{{ route('rapot.show', auth()->id()) }}"
           class="relative overflow-hidden flex shrink-0 items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
           {{ request()->routeIs('rapot.show') || request()->routeIs('rapot.challenges')
                ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>KARTU HASIL</span>

            <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ request()->routeIs('rapot.show') || request()->routeIs('rapot.challenges') ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
        </a>
        @endrole

        {{-- LOG SISTEM --}}
        @canany(['view log sistem', 'view all log sistem'])
        <a href="{{ route('activity-log.index') }}"
           class="relative overflow-hidden flex shrink-0 items-center gap-2.5 px-3 py-2.5 rounded-lg text-[12px] font-semibold tracking-wide transition
           {{ request()->routeIs('activity-log.*')
                ? 'bg-gray-400/15 text-[#30A192] light:bg-[#FAFAFA] light:shadow-sm light:text-[#003033]'
                : 'text-gray-400 hover:bg-[#DAF7F0] hover:text-[#2E377D] light:text-[#003033] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]' }}">
            <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l-4 5 4 5m8-10l4 5-4 5"/>
            </svg>
            <span>LOG SISTEM</span>

            <span class="absolute right-0 top-0 h-full w-[4px] rounded-l-full {{ request()->routeIs('activity-log.*') ? 'bg-[#30A192] light:bg-[#003033]' : 'bg-transparent' }}"></span>
        </a>
        @endcan

    </nav>

    <div class="px-3 pb-5 pt-1 border-t border-[#1e2130] light:border-[#1a2e28] flex flex-col shrink-0">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-[12px] font-semibold tracking-wide
                           text-[#FFB4AB] hover:bg-[#DAF7F0] hover:text-[#2E377D]
                               light:text-[#b3261e] light:hover:bg-black/5 light:hover:text-[#8c1d17] transition">
                <svg class="w-[15px] h-[15px] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </button>
        </form>

    </div>
</aside>
