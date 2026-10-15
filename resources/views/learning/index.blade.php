<x-app-layout>
    <x-slot name="header"><h2 class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#003033]">BELAJAR</h2></x-slot>

    <div class="mx-auto flex max-w-[1180px] flex-col gap-3.5 font-mono">
        {{-- ===================== HEADER WINDOW + FILTER ===================== --}}
        <section class="overflow-hidden border border-[#263239] light:border-[#b8b8b8]">
            <form method="GET" action="{{ route('learning.index') }}" class="flex min-h-[44px] flex-wrap items-center gap-3 bg-[#25272a] px-3 py-2 text-[10px] uppercase tracking-[0.14em] text-white light:bg-[#003033]">
                <div class="flex items-center gap-2 font-bold">
                    <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                    <span class="ml-1">DAFTAR_MATERI</span>
                </div>

                <div class="flex h-7 min-w-[160px] flex-1 items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white">
                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/></svg>
                    <input name="search" value="{{ request('search') }}" type="search" placeholder="Cari..." class="min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] text-gray-200 outline-none placeholder:text-gray-500 focus:ring-0 light:text-[#003033]">
                </div>

                <select name="kategori" onchange="this.form.submit()" class="h-7 min-w-[120px] border border-[#3b494b] bg-[#0f1017] px-3 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]">
                    <option value="">KATEGORI</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('kategori') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="level" onchange="this.form.submit()" class="h-7 min-w-[96px] border border-[#3b494b] bg-[#0f1017] px-3 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]">
                    <option value="">LEVEL</option>
                    <option value="easy" @selected(request('level') === 'easy')>MUDAH</option>
                    <option value="medium" @selected(request('level') === 'medium')>SEDANG</option>
                    <option value="hard" @selected(request('level') === 'hard')>SULIT</option>
                </select>

                <select name="tahun" onchange="this.form.submit()" class="h-7 min-w-[84px] border border-[#3b494b] bg-[#0f1017] px-3 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]">
                    <option value="">TAHUN</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @selected((string) request('tahun') === (string) $year)>{{ $year }}</option>
                    @endforeach
                </select>

                <select name="bulan" onchange="this.form.submit()" class="h-7 min-w-[84px] border border-[#3b494b] bg-[#0f1017] px-3 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]">
                    <option value="">BULAN</option>
                    @for($month = 1; $month <= 12; $month++)
                        <option value="{{ $month }}" @selected((string) request('bulan') === (string) $month)>{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>

                @if(filled(request('search')) || filled(request('kategori')) || filled(request('level')) || filled(request('tahun')) || filled(request('bulan')))
                    <a href="{{ route('learning.index') }}" aria-label="Reset filter" class="flex h-7 w-7 items-center justify-center border border-[#3b494b] text-[13px] font-bold text-gray-200 hover:border-[#30A192] hover:text-[#30A192] light:border-white light:text-white">X</a>
                @endif
            </form>
        </section>

        {{-- ===================== REKOMENDASI (bila ada) ===================== --}}
        @if($recommendations->isNotEmpty())
            <section>
                <div class="flex h-9 items-center px-2.5 bg-[#12141c] light:bg-[#003033] text-gray-300 light:text-white text-[10px] font-bold uppercase tracking-[0.12em]">Rekomendasi Untuk Anda</div>
                <div class="bg-[#0f1017] light:bg-[#ECECEC] px-2.5 py-3.5">
                    <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($recommendations as $recommendation)
                            @php
                                $recMaterial = $recommendation->material;
                                $recTitle = $recMaterial?->title ?? $recommendation->challenge?->title ?? $recommendation->category?->name ?? 'Rekomendasi';
                                $recLevel = $recMaterial?->level ?? 'medium';
                                $recDifficultyClass = match ($recLevel) {
                                    'easy' => 'border-emerald-400/60 text-emerald-400 light:text-emerald-600',
                                    'hard' => 'border-rose-400/60 text-rose-400 light:text-rose-600',
                                    default => 'border-amber-400/60 text-amber-400 light:text-amber-600',
                                };

                                // Samakan status dengan card materi: selesai/berjalan/baru.
                                $recProgress = $recMaterial ? ($progressByMaterial[$recMaterial->id] ?? null) : null;
                                $recStatus = $recProgress->status ?? 'not_started';
                                $recIsCompleted = $recStatus === 'completed';

                                if ($recMaterial) {
                                    $recStatusLabel = $recIsCompleted ? 'SELESAI' : ($recStatus === 'in_progress' ? 'BERLANJUT' : 'REKOMENDASI');
                                    $recButtonLabel = $recIsCompleted ? 'PELAJARI LAGI' : ($recStatus === 'in_progress' ? 'LANJUTKAN' : 'MULAI BELAJAR');
                                } else {
                                    $recStatusLabel = 'REKOMENDASI';
                                    $recButtonLabel = 'KERJAKAN SOAL';
                                }

                                $recStatusClass = $recIsCompleted ? 'text-[#4caf50] light:text-emerald-600' : 'text-[#14f0e6] light:text-[#003033]';
                                $recUrl = $recMaterial
                                    ? route('recommendations.material', $recommendation)
                                    : route('recommendations.question', $recommendation);
                            @endphp
                            <article class="flex min-h-[150px] flex-col rounded-md border border-[#263239] bg-[#161822] px-3 py-3.5 shadow-[0_0_16px_rgba(20,240,230,0.12)] light:border-[#555] light:bg-white light:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
                                <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.15em] {{ $recStatusClass }}">
                                    @if($recIsCompleted)
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Selesai"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>
                                    @else
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-width="1.8" d="M5 9.5a10 10 0 0 1 14 0M8 12.5a6 6 0 0 1 8 0M11 15.5a2 2 0 0 1 2 0"/>
                                            <circle cx="12" cy="19" r="1" fill="currentColor" stroke="none"/>
                                        </svg>
                                    @endif
                                    <span>{{ $recStatusLabel }}</span>
                                </div>

                                <h3 class="mt-1.5 truncate text-[13px] font-bold text-gray-200 light:text-[#003033]" title="{{ $recTitle }}">{{ $recTitle }}</h3>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="px-1.5 py-0.5 bg-[#252a2d] text-[8px] uppercase tracking-wide text-gray-400 light:bg-[#292929]">{{ $recommendation->category?->name ?? 'Learning' }}</span>
                                    <span class="border px-1.5 py-0.5 text-[8px] uppercase tracking-wide {{ $recDifficultyClass }}">{{ $recMaterial?->level_label ?? strtoupper($recLevel) }}</span>
                                </div>

                                <div class="mt-2 flex items-center gap-3 border-b border-[#2a3035] pb-2 text-[10px] text-gray-400 light:border-[#d1d5db] light:text-[#1a2e28]">
                                    @if($recMaterial)
                                        <span>{{ $recMaterial->questions()->count() }} SOAL</span>
                                        <span>{{ $recMaterial->attachments()->count() }} LAMPIRAN</span>
                                    @else
                                        <span>SKOR {{ $recommendation->average_score }}</span>
                                        <span>AMBANG {{ $recommendation->threshold }}</span>
                                    @endif
                                </div>

                                <div class="mt-auto pt-2.5 text-right">
                                    <a href="{{ $recUrl }}" class="inline-flex rounded px-3 py-1.5 text-[9px] font-bold uppercase text-white hover:opacity-90 {{ $recIsCompleted ? 'bg-[#4caf50] text-[#061015]' : 'bg-violet-500 light:bg-violet-600' }}">{{ $recButtonLabel }}</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ===================== DAFTAR MATERI ===================== --}}
        <section>
            <div class="flex h-9 items-center px-2.5 bg-[#12141c] light:bg-[#003033] text-gray-300 light:text-white text-[10px] font-bold uppercase tracking-[0.12em]">Materi Belajar</div>
            <div class="bg-[#0f1017] light:bg-[#ECECEC] px-2.5 py-3.5">
                <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
                    @forelse($materials as $material)
                        @php
                            $isCompleted = $material->my_status === 'completed';
                            $status = $isCompleted ? 'SELESAI' : ($material->my_status === 'in_progress' ? 'BERLANJUT' : 'BARU');
                            $buttonLabel = $isCompleted ? 'PELAJARI LAGI' : ($material->my_status === 'in_progress' ? 'LANJUTKAN' : 'MULAI BELAJAR');
                            $statusClass = $isCompleted ? 'text-[#4caf50] light:text-emerald-600' : 'text-[#14f0e6] light:text-[#003033]';
                            $difficultyClass = match ($material->level) {
                                'easy' => 'border-emerald-400/60 text-emerald-400 light:text-emerald-600',
                                'hard' => 'border-rose-400/60 text-rose-400 light:text-rose-600',
                                default => 'border-amber-400/60 text-amber-400 light:text-amber-600',
                            };
                        @endphp
                        <article class="flex min-h-[150px] flex-col rounded-md border border-[#263239] bg-[#161822] px-3 py-3.5 shadow-[0_0_16px_rgba(20,240,230,0.12)] light:border-[#555] light:bg-white light:shadow-[0_4px_12px_rgba(0,0,0,0.2)]">
                            <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.15em] {{ $statusClass }}">
                                @if($isCompleted)
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Selesai"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>
                                @else
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-width="1.8" d="M5 9.5a10 10 0 0 1 14 0M8 12.5a6 6 0 0 1 8 0M11 15.5a2 2 0 0 1 2 0"/>
                                        <circle cx="12" cy="19" r="1" fill="currentColor" stroke="none"/>
                                    </svg>
                                @endif
                                <span>{{ $status }}</span>
                            </div>

                            <h3 class="mt-1.5 truncate text-[13px] font-bold text-gray-200 light:text-[#003033]" title="{{ $material->title }}">{{ $material->title }}</h3>

                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="px-1.5 py-0.5 bg-[#252a2d] text-[8px] uppercase tracking-wide text-gray-400 light:bg-[#292929]">{{ $material->category?->name ?? 'Learning' }}</span>
                                <span class="border px-1.5 py-0.5 text-[8px] uppercase tracking-wide {{ $difficultyClass }}">{{ $material->level_label ?? strtoupper($material->level) }}</span>
                                @if($material->is_terbaru)
                                    <span class="px-1.5 py-0.5 bg-[#1e3a2f] text-[8px] uppercase tracking-wide text-[#30A192] light:bg-emerald-100 light:text-emerald-700">BARU</span>
                                @endif
                            </div>

                            <div class="mt-2 flex items-center gap-3 border-b border-[#2a3035] pb-2 text-[10px] text-gray-400 light:border-[#d1d5db] light:text-[#1a2e28]">
                                <span>{{ $material->questions_count }} SOAL</span>
                                <span>{{ $material->attachments_count }} LAMPIRAN</span>
                            </div>

                            <div class="mt-auto pt-2.5 text-right">
                                <a href="{{ route('learning.show', $material) }}" class="inline-flex rounded px-3 py-1.5 text-[9px] font-bold uppercase text-white hover:opacity-90 {{ $isCompleted ? 'bg-[#4caf50] text-[#061015]' : 'bg-violet-500 light:bg-violet-600' }}">{{ $buttonLabel }}</a>
                            </div>
                        </article>
                    @empty
                        <div class="py-10 text-center text-[11px] text-gray-500 sm:col-span-2 lg:col-span-4">Belum ada materi belajar.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
