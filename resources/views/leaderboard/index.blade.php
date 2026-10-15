<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">PAPAN PERINGKAT</h2></x-slot>

    <div class="mx-auto w-full max-w-[1140px] min-w-0 font-mono">
        <div class="mb-4 w-full max-w-[270px] border-l-4 border-[#a855f7] bg-[#1b1d1f] px-5 py-4 light:bg-white">
            <div class="text-[10px] uppercase tracking-[0.16em] text-gray-500 light:text-[#6b7280]">Total Tantangan</div>
            <div class="mt-1 text-4xl font-light text-[#a855f7]">{{ number_format($totalChallenges) }}</div>
        </div>

        <section class="overflow-hidden border border-[#263239] bg-black light:border-[#b8b8b8] light:bg-white">
            <div class="flex min-h-[58px] flex-wrap items-center gap-3 border-b border-[#3b494b] bg-[#25272a] px-4 py-3 light:border-[#003033] light:bg-[#003033]">
                <div class="mr-auto flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200 light:text-white"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-1">Papan Peringkat // Daftar Challenge</span></div>
                <form method="GET" action="{{ route('leaderboard.index') }}" class="flex w-full flex-wrap justify-end gap-2 lg:w-auto">
                    <select name="category_id" onchange="this.form.submit()" class="h-8 w-full border border-[#455158] bg-[#101215] px-3 py-0 text-left text-[10px] uppercase tracking-[0.08em] text-gray-200 outline-none sm:w-40 light:bg-white light:text-[#003033]"><option value="">KATEGORI</option>@foreach($categoryOptions as $category)<option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>
                    <select name="year" onchange="this.form.submit()" class="h-8 w-full border border-[#455158] bg-[#101215] px-3 py-0 text-left text-[10px] uppercase tracking-[0.08em] text-gray-200 outline-none sm:w-24 light:bg-white light:text-[#003033]"><option value="">TAHUN</option>@for($year = now()->year; $year >= now()->year - 5; $year--)<option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>{{ $year }}</option>@endfor</select>
                    <select name="month" onchange="this.form.submit()" class="h-8 w-full border border-[#455158] bg-[#101215] px-3 py-0 text-left text-[10px] uppercase tracking-[0.08em] text-gray-200 outline-none sm:w-24 light:bg-white light:text-[#003033]"><option value="">BULAN</option>@for($month = 1; $month <= 12; $month++)<option value="{{ $month }}" @selected((string) $selectedMonth === (string) $month)>{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</option>@endfor</select>
                    @if($selectedCategory || $selectedYear || $selectedMonth)<a href="{{ route('leaderboard.index') }}" aria-label="Reset filter" class="flex h-8 w-8 items-center justify-center border border-[#455158] bg-transparent text-[14px] font-bold text-gray-300 hover:border-[#30A192] hover:text-[#30A192] light:bg-white light:text-[#003033]">X</a>@endif
                </form>
            </div>
<form method="GET" action="{{ route('leaderboard.index') }}" class="border-b border-[#384147] bg-[#161822] p-3 light:bg-[#9CA3AF]">
                <input type="hidden" name="category_id" value="{{ $selectedCategory }}"><input type="hidden" name="year" value="{{ $selectedYear }}"><input type="hidden" name="month" value="{{ $selectedMonth }}">
                <div class="flex h-10 items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white"><svg class="h-5 w-5 shrink-0 text-[#9CA3AF]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="6" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m16 16 4 4"/></svg><input name="search" value="{{ $search }}" type="search" placeholder="CARI..." class="min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] tracking-[0.1em] text-gray-200 outline-none placeholder:text-gray-500 focus:ring-0 light:text-[#003033] light:placeholder:text-[#6b7280]"></div>
            </form>
            <div class="overflow-x-auto"><table class="w-full min-w-[680px] border-collapse"><thead><tr class="border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]"><th class="px-4 py-3 text-center text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">ID</th><th class="px-4 py-3 text-center text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Kategori</th><th class="px-4 py-3 text-center text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Judul</th><th class="px-4 py-3 text-center text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Tanggal</th><th class="px-4 py-3 text-center text-[10px] uppercase tracking-wide text-gray-300 light:text-[#003033]">Aksi</th></tr></thead><tbody>
                @forelse($challenges as $challenge)<tr class="border-b border-[#20272a] hover:bg-white/[0.04] light:border-gray-200 light:bg-white light:hover:bg-gray-50"><td class="px-4 py-3 text-center text-[11px] text-gray-300 light:text-[#003033]">[{{ str_pad($challenge->id, 3, '0', STR_PAD_LEFT) }}]</td><td class="px-4 py-3 text-center"><span class="border border-gray-500 px-2 py-1 text-[9px] uppercase text-gray-300 light:border-[#003033] light:text-[#003033]">{{ $challenge->category?->name ?? 'Tanpa kategori' }}</span></td><td class="px-4 py-3 text-left text-[11px] font-semibold uppercase text-gray-200 light:text-[#003033]">{{ $challenge->title }}</td><td class="px-4 py-3 text-center text-[10px] uppercase text-gray-400 light:text-[#003033]">{{ $challenge->created_at?->translatedFormat('D, d F Y') }}</td><td class="px-4 py-3 text-center"><a href="{{ route('leaderboard.show', $challenge) }}" aria-label="Lihat leaderboard {{ $challenge->title }}" class="inline-flex h-6 w-5 items-center justify-center border border-blue-300 bg-[#DBF4FF] text-[#075985]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.5 12s3.5-5.5 9.5-5.5s9.5 5.5 9.5 5.5s-3.5 5.5-9.5 5.5-9.5-5.5-9.5-5.5Z"/><circle cx="12" cy="12" r="2.5" stroke-width="2"/></svg></a></td></tr>@empty<tr><td colspan="5" class="px-6 py-16 text-center text-[11px] text-gray-500">Belum ada challenge.</td></tr>@endforelse
            </tbody></table></div>
        </section>
        @php
            $pageUrl = fn ($p) => $challenges->url($p);
        @endphp
        <div class="mt-5 flex items-center justify-between flex-wrap gap-2 border border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
            <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                Total_Data: {{ number_format($challenges->total()) }}
                &nbsp;·&nbsp;
                Menampilkan: {{ str_pad($challenges->firstItem() ?? 0, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($challenges->lastItem() ?? 0, 2, '0', STR_PAD_LEFT) }}
            </span>
            <div class="flex items-center gap-1">
                <a href="{{ $challenges->onFirstPage() ? '#' : $pageUrl($challenges->currentPage() - 1) }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $challenges->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&lsaquo;</a>
                @php
                    $start = max(1, min($challenges->currentPage() - 1, $challenges->lastPage() - 2));
                    $end = min($challenges->lastPage(), $start + 2);
                @endphp
                @for($p = $start; $p <= $end; $p++)
                    <a href="{{ $pageUrl($p) }}" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition {{ $p === $challenges->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}</a>
                @endfor
                <a href="{{ $challenges->hasMorePages() ? $pageUrl($challenges->currentPage() + 1) : '#' }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $challenges->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&rsaquo;</a>
            </div>
        </div>
    </div>
</x-app-layout>
