@php
    $user = Auth::user();
    $userName = $user?->name ?? '';
    $userRole = $user?->getRoleNames()->first() ?? 'User';
@endphp

@if(!$user || trim($userName) === '')
    <form id="auto-logout-form" method="POST" action="{{ route('logout') }}">
        @csrf
    </form>
    <script>
        document.getElementById('auto-logout-form').submit();
    </script>
@else
    <div class="flex items-center gap-3.5"
         x-data="{ theme: localStorage.getItem('theme') || 'dark' }"
         x-init="
             document.documentElement.classList.toggle('light', theme === 'light');
             $watch('theme', value => {
                 localStorage.setItem('theme', value);
                 document.documentElement.classList.toggle('light', value === 'light');
             });
         ">

        <button type="button"
                @click="theme = (theme === 'light' ? 'dark' : 'light')"
                class="w-[34px] h-[34px] flex items-center justify-center rounded-full
                       text-[#B9CACB] hover:bg-[#DAF7F0] hover:text-[#2E377D] transition
                       light:text-[#003033] light:hover:text-[#2E377D] light:hover:bg-[#EEDAF7]">
            <svg x-show="theme === 'dark'" class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg x-show="theme === 'light'" x-cloak class="w-[16px] h-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
        </button>

        <div class="w-[1px] h-[30px] bg-[#0d0e12] light:bg-[#003033]"></div>

        <a href="{{ route('profile.index') }}"
           class="flex items-center gap-2.5 px-2 py-1 rounded-lg no-underline transition
                  hover:bg-[#DAF7F0] hover:text-[#2E377D] light:hover:bg-[#EEDAF7] light:hover:text-[#2E377D]">

            <div class="text-right leading-tight">
                <div class="text-[13px] font-semibold text-slate-200 light:text-[#003033] whitespace-nowrap">
                    {{ $userName }}
                </div>
                <div class="text-[11px] font-medium text-gray-500 light:text-[#003033] whitespace-nowrap">
                    {{ $userRole }}
                </div>
            </div>

            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-teal-400 to-teal-700
                        flex items-center justify-center text-[13px] font-bold text-teal-950 shrink-0 overflow-hidden">
                @if($user->image)
                    <img src="{{ $user->image_url }}"
                         alt="{{ $userName }}"
                         class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($userName, 0, 1)) }}
                @endif
            </div>
        </a>
    </div>
@endif