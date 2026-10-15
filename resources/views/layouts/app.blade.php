<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-screen overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <script>
        (function () {
            var theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light');
            }
        })();
    </script>

    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Figtree',sans-serif] bg-[#0d0e12] light:bg-[#ECECEC] antialiased transition-colors h-screen overflow-hidden">

    <div class="grid grid-cols-[240px_minmax(0,1fr)] grid-rows-[1fr] h-screen overflow-hidden bg-[#0d0e12] light:bg-[#ECECEC] transition-colors">

        @include('layouts.navigation')

        <div class="flex flex-col min-h-0 overflow-hidden">

            <header class="shrink-0 bg-[#161822] light:bg-[#F4F4F5] border-b border-[#1e2130] light:border-[#003033] px-6 h-14 flex items-center justify-between transition-colors">
                <div class="text-[17px] font-extrabold text-slate-50 light:text-[#1a2e28] leading-[1.2] tracking-tight text-sm uppercase">
                    {{ $header ?? '' }}
                </div>

                @include('layouts.navbar')
            </header>

            <main class="flex-1 overflow-y-auto min-h-0 p-4 xl:p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <x-flash-message />
                {{ $slot }}
            </main>

        </div>

    </div>

</body>
</html>