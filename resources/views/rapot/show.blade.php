<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KARTU HASIL</h2></x-slot>

    <div class="mx-auto w-full max-w-[1140px] min-w-0 font-mono">
        <section class="overflow-hidden bg-black light:bg-white">
            <div class="flex min-h-12 flex-wrap items-center gap-2 border-b border-[#3b494b] bg-[#25272a] px-3 py-2 light:border-[#003033] light:bg-[#003033]"><div class="mr-auto flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fuchsia-500"></span><span class="h-2 w-2 rounded-full bg-cyan-400"></span><span class="h-2 w-2 rounded-full bg-amber-400"></span><span class="ml-1 text-[11px] font-bold uppercase tracking-[0.14em] text-gray-200 light:text-white">Daftar Kategori //</span></div><form method="GET" action="{{ route('rapot.show', $user->id) }}"><select name="category" onchange="this.form.submit()" class="h-9 w-40 appearance-none border border-[#455158] bg-[#101215] px-3 py-1 text-[10px] leading-normal uppercase tracking-wide text-gray-200 outline-none light:bg-white light:text-[#003033]"><option value="">KATEGORI</option>@foreach(\App\Models\Category::active()->orderBy('name')->get() as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></form></div>

            <div class="space-y-1.5 bg-[#0f1017] p-2 light:bg-[#ECECEC]">
                @forelse($categoryScores as $row)
                    @php
                        $score = (int) $row->average_score;
                        $threshold = $row->category->point_ambang_batas ?? 60;
                        $isPassed = $row->attempts_count >= 3 && $score >= $threshold;
                        $barColor = $row->progress_percent <= 40 ? '#ff3038' : ($row->progress_percent <= 70 ? '#e2c400' : '#30A192');
                        $segments = min(3, max(0, (int) ceil($row->completed_count / 1)));
                    @endphp
                    <article class="mx-auto w-[92%] rounded-xl border border-[#3b3f46] bg-[#1b1d1f] px-3 py-1.5 light:border-[#9ca3af] light:bg-white">
                        <div class="flex items-center justify-between gap-3"><div class="min-w-0"><h3 class="truncate text-[12px] font-bold uppercase text-fuchsia-400">{{ $row->category->name }}</h3><p class="mt-0.5 text-[10px] uppercase text-gray-300 light:text-[#003033]">{{ min(3, $row->completed_count) }}/3 Tantangan Selesai</p></div><span class="shrink-0 text-[12px] font-bold" style="color: {{ $barColor }}">{{ $row->progress_percent }}%</span></div>
                        <div class="mx-auto mt-2 flex w-1/2 gap-1">@for($segment = 1; $segment <= 3; $segment++)<span class="h-1 flex-1" style="background: {{ $segment <= $segments ? $barColor : '#34383f' }}"></span>@endfor</div>
                        <div class="mt-1 flex items-center justify-between border-t border-[#384147]/60 pt-1 light:border-gray-200"><div class="flex items-baseline gap-2"><span class="text-[9px] font-bold uppercase text-gray-500">Skor:</span><span class="text-[12px] font-bold text-gray-500 light:text-[#003033]">{{ number_format($score) }}</span></div><a href="{{ route('rapot.challenges', ['user' => $user->id, 'category' => $row->kategori_id]) }}" class="inline-flex items-center gap-1 text-[9px] font-bold uppercase text-gray-300 hover:text-[#30A192] light:text-[#003033]">LIHAT DETAIL <span class="text-[13px] leading-none">→</span></a></div>
                    </article>
                @empty
                    <div class="px-6 py-12 text-center text-[10px] text-gray-500">Belum ada data kategori.</div>
                @endforelse
            </div>
        </section>

        @php
            $pageUrl = fn ($p) => $categoryScores->url($p);
        @endphp
        <div class="mt-5 flex items-center justify-between flex-wrap gap-2 border border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
            <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                Total_Data: {{ number_format($categoryScores->total()) }}
                &nbsp;·&nbsp;
                Menampilkan: {{ str_pad($categoryScores->firstItem() ?? 0, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($categoryScores->lastItem() ?? 0, 2, '0', STR_PAD_LEFT) }}
            </span>
            <div class="flex items-center gap-1">
                <a href="{{ $categoryScores->onFirstPage() ? '#' : $pageUrl($categoryScores->currentPage() - 1) }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $categoryScores->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&lsaquo;</a>
                @php
                    $start = max(1, min($categoryScores->currentPage() - 1, $categoryScores->lastPage() - 2));
                    $end = min($categoryScores->lastPage(), $start + 2);
                @endphp
                @for($p = $start; $p <= $end; $p++)
                    <a href="{{ $pageUrl($p) }}" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition {{ $p === $categoryScores->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">{{ str_pad($p, 2, '0', STR_PAD_LEFT) }}</a>
                @endfor
                <a href="{{ $categoryScores->hasMorePages() ? $pageUrl($categoryScores->currentPage() + 1) : '#' }}" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition {{ $categoryScores->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5' }}">&rsaquo;</a>
            </div>
        </div>
    </div>
</x-app-layout>
