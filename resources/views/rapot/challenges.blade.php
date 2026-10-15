<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">DETAIL KARTU HASIL</h2></x-slot>

    @php
        $completed = $attempts->where('status', 'completed')->count();
        $completedDisplay = str_pad(min(3, $completed), 2, '0', STR_PAD_LEFT);
        $totalScore = (int) round($attempts->where('status', 'completed')->sum(fn ($attempt) => (float) $attempt->total_score));
        $averageScore = $completed > 0 ? round($attempts->where('status', 'completed')->avg('total_score'), 2) : 0;
        $threshold = $category?->point_ambang_batas ?? 60;
        $isPassed = $completed >= 3 && $averageScore >= $threshold;
        $statusColor = $isPassed ? '#30A192' : '#ff3038';
    @endphp

    <div class="mx-auto w-full max-w-[860px] min-w-0 font-mono">
        <div class="mb-5 border-b border-[#3b494b] pb-3 light:border-[#9ca3af]">
            <h1 class="text-[22px] font-extrabold uppercase tracking-tight text-slate-100 light:text-[#003033]">RAPORT KATEGORI: {{ $categoryLabel }}</h1>
        </div>

        <div class="mb-5 flex flex-wrap items-start gap-4">
            {{-- KOLOM KIRI: 3 card + tabel --}}
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap gap-4">
                    {{-- Tantangan Selesai --}}
                    <div class="flex h-[104px] min-w-[140px] flex-1 flex-col justify-between border border-[#3b494b] bg-[#1b1d1f] px-4 py-3 light:border-[#9ca3af] light:bg-white">
                        <div class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-gray-200 light:text-black">Tantangan Selesai</div>
                        <div class="text-[24px] font-bold leading-none text-[#20c5e2]">{{ $completedDisplay }} <span class="text-[11px] font-normal text-gray-500">/ 03</span></div>
                        <div class="flex gap-1.5">
                            @for($segment = 1; $segment <= 3; $segment++)
                                <span class="h-1 flex-1" style="background: {{ $segment <= min(3, $completed) ? '#20c5e2' : '#34383f' }}"></span>
                            @endfor
                        </div>
                    </div>

                    {{-- Total Nilai Kategori --}}
                    <div class="flex h-[104px] min-w-[140px] flex-1 flex-col justify-between border border-[#3b494b] bg-[#1b1d1f] px-4 py-3 light:border-[#9ca3af] light:bg-white">
                        <div class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-gray-200 light:text-black">Total Nilai Kategori</div>
                        <div class="text-[24px] font-bold leading-none text-[#a855f7]">{{ number_format($totalScore) }} <span class="text-[11px] font-normal text-gray-500">PTS</span></div>
                        <div class="h-1 bg-[#34383f] light:bg-[#e5e7eb]">
                            <div class="h-full bg-[#a855f7]" style="width: {{ min(100, (int) $averageScore) }}%"></div>
                        </div>
                    </div>

                    {{-- Rata-Rata Nilai --}}
                    <div class="flex h-[104px] min-w-[140px] flex-1 flex-col justify-between border border-[#3b494b] bg-[#1b1d1f] px-4 py-3 light:border-[#9ca3af] light:bg-white">
                        <div class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-gray-200 light:text-black">Rata-Rata Nilai</div>
                        <div class="text-[24px] font-bold leading-none text-[#38bdf8]">{{ number_format($averageScore, 2) }} <span class="text-[11px] font-normal text-gray-500">%</span></div>
                        <div class="text-[9px] font-bold text-gray-200 light:text-black">Nilai ambang batas: <span class="text-[11px] font-bold text-[#38bdf8]">{{ number_format($threshold, 1) }}</span></div>
                    </div>
                </div>

                {{-- Tabel: lebar mengikuti 3 card --}}
                <section class="mt-5 overflow-hidden bg-black light:bg-white">
                    <div class="flex h-9 items-center gap-2 bg-[#25272a] px-3 light:bg-[#003033]">
                        <span class="h-2 w-2 rounded-full bg-fuchsia-500"></span>
                        <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        <span class="ml-1 text-[9px] font-bold uppercase tracking-[0.14em] text-gray-200 light:text-white">RINGKASAN NILAI TANTANGAN</span>
                    </div>
                    <div class="overflow-x-auto border border-t-0 border-[#3b494b] light:border-[#b8b8b8]">
                        <table class="w-full min-w-[480px] border-collapse">
                            <thead>
                                <tr class="border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]">
                                    <th class="py-2.5 pl-24 pr-4 text-left text-[9px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Judul Challenge</th>
                                    <th class="w-[90px] px-4 py-2.5 text-center text-[9px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Skor</th>
                                    <th class="w-[110px] py-2.5 pl-4 pr-24 text-center text-[9px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attempts as $attempt)
                                    <tr class="border-b border-[#20272a] light:border-gray-200 light:bg-white">
                                        <td class="py-3 pl-24 pr-4 text-left text-[10px] font-semibold uppercase text-gray-200 light:text-[#003033]">{{ $attempt->challenge?->title ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-[11px] font-bold text-gray-200 light:text-[#003033]">{{ number_format((float) $attempt->total_score, 0) }}</td>
                                        <td class="py-3 pl-4 pr-24 text-center">
                                            @if($attempt->status === 'completed')
                                                <span class="inline-block bg-[#1e3a2f] px-2 py-0.5 text-[9px] text-[#30A192] light:bg-emerald-100 light:text-emerald-700">Selesai</span>
                                            @else
                                                <span class="inline-block bg-[#3a1e20] px-2 py-0.5 text-[9px] text-[#ff3038] light:bg-red-100 light:text-red-700">Belum Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-[10px] text-gray-500">Belum ada challenge pada kategori ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            {{-- KOLOM KANAN: status kelulusan (berdiri sendiri, ukuran mandiri) --}}
            <div class="w-[150px] shrink-0 self-start">
                <div class="flex h-[80px] flex-col border border-[#3b494b] bg-[#1b1d1f] px-4 py-2 light:border-[#9ca3af] light:bg-white">
                    <div class="text-[9px] font-extrabold uppercase leading-[1.4] tracking-[0.14em] text-gray-200 light:text-black">Status<br>Kelulusan</div>
                    <div class="mt-1.5 flex items-center gap-1.5" style="color: {{ $statusColor }}">
                        @if($isPassed)
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.5 12.5 2.5 2.5 4.5-5.5"/></svg>
                        @else
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 9 6 6m0-6-6 6"/></svg>
                        @endif
                        <span class="text-[17px] font-bold leading-none">{{ $isPassed ? 'LULUS' : 'TIDAK LULUS' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('rapot.show', $user->id) }}" class="mt-1 inline-flex h-9 items-center gap-2 border border-[#30A192] px-4 text-[9px] font-bold uppercase tracking-[0.14em] text-[#30A192] hover:bg-[#30A192]/10 light:border-[#003033] light:text-[#003033]">
            <span>&#8592;</span> KEMBALI KE DAFTAR KARTU HASIL
        </a>
    </div>
</x-app-layout>
