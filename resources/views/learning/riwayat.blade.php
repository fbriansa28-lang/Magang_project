<x-app-layout>
    <x-slot name="header"><h2 class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#003033]">BELAJAR</h2></x-slot>

    @php
        $total = (int) ($progress->total_questions ?? $material->questions->count());
        $correct = (int) ($progress->correct_count ?? 0);
        $score = $total > 0 ? (int) round($correct / $total * 100) : 0;
        $isPassed = $score >= 75;
        $correctSegments = $total > 0 ? (int) round($correct / $total * 10) : 0;
        $levelLabel = match ($material->level) {
            'easy' => 'MUDAH',
            'medium' => 'SEDANG',
            default => 'SULIT',
        };
        $levelClass = match ($material->level) {
            'easy' => 'bg-[#4caf50] text-[#061015]',
            'medium' => 'bg-[#ffd166] text-[#3a2a00]',
            default => 'bg-[#f87171] text-[#3a0500]',
        };
    @endphp

    <div class="mx-auto w-full max-w-[1080px] min-w-0 font-mono">
        {{-- ===================== BAGIAN ATAS: 3 KARTU STATISTIK ===================== --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            {{-- SKOR YANG DI DAPAT --}}
            <div class="border border-[#263239] bg-[#1b1d1f] px-4 py-3 light:border-[#3b494b] light:bg-white">
                <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-gray-400 light:text-gray-600">Skor Yang Di Dapat</div>
                <p class="mt-2 text-[30px] font-bold leading-none text-[#20c5e2]">{{ $score }}</p>
            </div>

            {{-- TOTAL JAWABAN BENAR --}}
            <div class="border border-[#263239] bg-[#1b1d1f] px-4 py-3 light:border-[#3b494b] light:bg-white">
                <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-gray-400 light:text-gray-600">Total Jawaban Benar</div>
                <p class="mt-2 text-[30px] font-bold leading-none text-[#4caf50]">{{ $correct }}</p>
            </div>

            {{-- TOTAL JAWABAN SALAH --}}
            <div class="border border-[#263239] bg-[#1b1d1f] px-4 py-3 light:border-[#3b494b] light:bg-white">
                <div class="text-[9px] font-bold uppercase tracking-[0.14em] text-gray-400 light:text-gray-600">Total Jawaban Salah</div>
                <p class="mt-2 text-[30px] font-bold leading-none text-[#ff3038]">{{ max(0, $total - $correct) }}</p>
            </div>
        </div>

        {{-- ===================== BARIS BAWAH: SOAL + NAVIGATOR ===================== --}}
        <div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_290px]">

            {{-- KOLOM KIRI: CARD SOAL --}}
            <div class="flex min-w-0 flex-col gap-3">

                {{-- CARD MATERI (judul + kategori + deskripsi) --}}
                <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#263239] px-4 py-3 light:border-[#3b494b]">
                        <h2 class="text-[15px] font-extrabold uppercase tracking-tight text-[#30A192]">{{ $material->title }}</h2>
                        <span class="border border-[#3b494b] px-2 py-0.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">{{ $material->category?->name ?? 'Umum' }}</span>
                    </div>
                    @if(filled($material->description) && $material->description !== $material->content)
                        <p class="px-4 py-3 text-[11px] leading-5 text-gray-300 light:text-[#1a2e28]">{{ $material->description }}</p>
                    @endif
                </section>

                @foreach($material->questions as $index => $question)
                    @php
                        $submission = $submissionByQuestion->get($question->id);
                        $isCorrect = (bool) ($submission->is_correct ?? false);
                        $isPg = $question->isPilihanGanda();
                        $options = $isPg ? ($question->options() ?? []) : [];
                        $correctOption = strtolower((string) ($question->correct_option ?? ''));
                        $chosen = strtolower((string) ($submission->submitted_answer ?? ''));
                    @endphp

                    {{-- CARD SOAL + JAWABAN (satu kontainer) --}}
                    <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                        {{-- Badge kanan-atas: status benar/salah --}}
                        <div class="flex flex-wrap items-center justify-end gap-2 px-4 pt-3">
                            <span class="border px-1.5 py-0.5 text-[8px] font-bold uppercase {{ $isCorrect ? 'border-[#4caf50] bg-[#4caf50] text-[#061015]' : 'border-[#ff3038] bg-[#ff3038] text-white' }}">
                                {{ $isCorrect ? 'BENAR' : 'SALAH' }}
                            </span>
                        </div>

                        <h3 class="px-4 pb-3 pt-2 text-[12px] font-bold leading-5 text-gray-200 light:text-[#003033]">
                            {{ $index + 1 }}. {{ $question->question }}
                        </h3>

                        {{-- Garis bawah soal --}}
                        <div class="mx-4 border-t border-[#263239] light:border-[#3b494b]"></div>

                        {{-- Jawaban peserta --}}
                        <div class="px-4 pb-3 pt-3">
                            <p class="mb-1.5 text-[8px] font-bold uppercase tracking-[0.14em] text-gray-500 light:text-gray-600">JAWABAN</p>

                            @if($isPg)
                                <div class="flex flex-col gap-1.5">
                                    @foreach($options as $key => $text)
                                        @php
                                            $isKeyCorrect = strtolower((string) $key) === $correctOption;
                                            $isKeyChosen = strtolower((string) $key) === $chosen;
                                            $optClass = 'border-[#3b494b] bg-[#0f1017] text-gray-300 light:bg-[#ECECEC] light:text-[#1a2e28]';
                                            if ($isKeyCorrect) {
                                                $optClass = 'border-[#4caf50] bg-[#4caf50] text-[#061015] font-bold';
                                            } elseif ($isKeyChosen) {
                                                $optClass = 'border-[#ff3038] bg-[#ff3038] text-white font-bold';
                                            }
                                        @endphp
                                        <div class="border px-3 py-1.5 text-[11px] {{ $optClass }}">{{ $text }}</div>
                                    @endforeach
                                </div>
                            @else
                                @php
                                    $answerClass = $isCorrect
                                        ? 'border-[#4caf50] bg-[#4caf50] text-[#061015] font-bold'
                                        : 'border-[#ff3038] bg-[#ff3038] text-white font-bold';
                                @endphp
                                <div class="border px-3 py-2 text-[11px] {{ $answerClass }}">{{ $submission->submitted_answer ?: '-' }}</div>

                                <p class="mb-1.5 mt-3 text-[8px] font-bold uppercase tracking-[0.14em] text-gray-500 light:text-gray-600">KUNCI JAWABAN</p>
                                <div class="border border-[#3b494b] bg-[#0f1017] px-3 py-2 text-[11px] text-gray-300 light:bg-[#ECECEC] light:text-[#1a2e28]">{{ $question->answer_key ?? '-' }}</div>
                            @endif
                        </div>

                        {{-- KETERANGAN JAWABAN (teks di bawah jawaban, seperti halaman result) --}}
                        <div class="mx-4 border-t border-[#263239] light:border-[#3b494b]"></div>
                        <div class="px-4 py-3 text-[10px] leading-4 text-gray-400 light:text-gray-600">
                            @if($isPg)
                                @if(filled($question->hint))
                                    <p>Mengapa jawaban {{ strtoupper($chosen ?: '-') }} {{ $isCorrect ? 'benar' : 'salah' }}? {{ $question->hint }}</p>
                                @endif
                                @if($correctOption !== '')
                                    <p class="{{ filled($question->hint) ? 'mt-1.5' : '' }}">Sehingga jawaban yang benar adalah {{ strtoupper($correctOption) }}</p>
                                @endif
                            @else
                                @php
                                    $similarity = filled($submission->submitted_answer ?? null) && filled($question->answer_key)
                                        ? \App\Models\Simulasi::textSimilarity((string) $submission->submitted_answer, (string) $question->answer_key)
                                        : 0;
                                @endphp
                                <p>Hasil jawaban memiliki kemiripan similarity sebesar {{ round($similarity) }} % sehingga hasil jawaban dianggap {{ $isCorrect ? 'benar' : 'salah' }}</p>
                                @if(filled($question->hint))
                                    <p class="mt-1.5">Keterangan: {{ $question->hint }}</p>
                                @endif
                            @endif
                        </div>
                    </section>
                @endforeach
            </div>

            {{-- ===================== KOLOM KANAN: NAVIGATOR ===================== --}}
            <aside class="flex min-w-0 flex-col gap-3">
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-3 light:border-[#3b494b] light:bg-white">
                    <div class="flex flex-wrap items-center justify-center gap-3 text-[8px] uppercase text-gray-400 light:text-gray-600">
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-[#ff3038]"></span> Jawaban Salah</span>
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-[#4caf50]"></span> Jawaban Benar</span>
                    </div>

                    <div class="mt-3 grid grid-cols-6 gap-1.5">
                        @foreach($material->questions as $index => $question)
                            @php
                                $submission = $submissionByQuestion->get($question->id);
                                $navClass = ($submission->is_correct ?? false)
                                    ? 'border-[#4caf50] bg-[#4caf50] text-[#061015]'
                                    : 'border-[#ff3038] bg-[#ff3038] text-white';
                            @endphp
                            <span class="flex aspect-square items-center justify-center border text-[10px] font-bold {{ $navClass }}">{{ $index + 1 }}</span>
                        @endforeach
                    </div>
                </section>

                <a href="{{ route('learning.index') }}"
                   class="flex h-9 w-full items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] hover:bg-[#174b96]">DAFTAR MATERI</a>
            </aside>
        </div>
    </div>
</x-app-layout>
