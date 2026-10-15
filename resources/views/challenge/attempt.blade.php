<x-app-layout>
    <x-slot name="header"><h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">CHALLENGE</h2></x-slot>

    @can('start challenge')
    <div class="mx-auto w-full max-w-[1080px] min-w-0 font-mono" id="attemptApp"
         data-submit-url="{{ route('attempts.submit-answer', $attempt) }}"
         data-hint-url-template="{{ route('attempt-questions.hint', ['attemptQuestion' => '__AQ__']) }}"
         data-finish-url="{{ route('attempts.finish', $attempt) }}"
         data-remaining-seconds="{{ $remainingSeconds }}">

        <div id="noticeBox" class="mb-3 hidden border px-4 py-2 text-[11px]"></div>

        <div class="grid grid-cols-1 items-start gap-3 lg:grid-cols-[minmax(0,1fr)_290px]">

            {{-- ===================== KOLOM KIRI: SOAL + JAWABAN =====================
                 Tinggi & isi mengikuti kondisi soal (pendek/panjang); tanpa
                 scroll internal agar ukuran soal dan jawaban tetap penuh. --}}
            <div class="flex min-w-0 flex-col gap-3">
                @foreach($questions as $index => $question)
                    @php
                        $isPg = $question['type'] === 'pilihan_ganda';
                        $isFinal = $question['final'];
                        $isCorrect = $question['status'] === 'correct';
                        $difficultyLabel = ['easy' => 'MUDAH', 'medium' => 'SEDANG', 'hard' => 'SULIT'][$question['difficulty']] ?? strtoupper((string) $question['difficulty']);
                        // Benar/salah TIDAK ditampilkan (PG maupun uraian); hanya WAKTU HABIS.
                        $statusLabel = $question['status'] === 'time_expired' ? 'WAKTU HABIS' : '';
                        $statusClass = match ($question['status']) {
                            'correct' => 'border-[#30A192] bg-[#30A192]/15 text-[#80fff2] light:text-emerald-700',
                            'wrong' => 'border-[#ff3038]/60 bg-[#ff3038]/10 text-[#ff9a9e] light:text-red-700',
                            'time_expired' => 'border-gray-500 bg-gray-500/10 text-gray-400',
                            default => '',
                        };
                    @endphp

                    <div class="attempt-question {{ $index === 0 ? 'flex' : 'hidden' }} min-w-0 flex-col gap-3"
                         data-attempt-question-id="{{ $question['id'] }}"
                         data-index="{{ $index }}"
                         data-is-pg="{{ $isPg ? '1' : '0' }}"
                         data-difficulty-label="{{ $difficultyLabel }}"
                         data-has-hint="{{ $question['has_hint'] ? '1' : '0' }}"
                         data-hint-opened="{{ $question['hint_opened'] ? '1' : '0' }}"
                         data-hint="{{ $question['hint'] ?? '' }}"
                         data-final="{{ $isFinal ? '1' : '0' }}"
                         data-status="{{ $question['status'] }}"
                         data-score="{{ number_format((float) ($question['score'] ?? 0), 2, '.', '') }}"
                         data-wrong-count="{{ $question['wrong_count'] }}">

                        {{-- KARTU SOAL --}}
                        <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                            <div class="relative px-5 pb-4 pt-4">
                                <h1 class="max-w-[400px] text-[19px] font-extrabold leading-[1.15] tracking-tight text-[#30A192]">
                                    {{ $attempt->challenge->title }}
                                </h1>

                                <p class="mt-2 max-w-[560px] text-[11.5px] leading-5 text-gray-300 light:text-[#1a2e28]">
                                    {{ $attempt->challenge->description }}
                                </p>

                                {{-- Blok gaya terminal berisi konten soal --}}
                                <div class="mt-3 border border-[#3b494b] bg-[#0f1017] light:bg-[#ECECEC]">
                                    <div class="flex h-6 items-center gap-2 border-b border-[#3b494b] bg-[#25272a] px-3 light:border-[#003033] light:bg-[#003033]">
                                        <span class="h-2 w-2 rounded-full bg-[#ff5f9e]"></span>
                                        <span class="h-2 w-2 rounded-full bg-[#ffd166]"></span>
                                        <span class="h-2 w-2 rounded-full bg-[#2ee6a8]"></span>
                                    </div>
                                    <div class="px-4 py-4 text-center">
                                        <p class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-500 light:text-gray-600">ALIRAN DATA TERENKRIPSI</p>
                                        <p class="mx-auto mt-2 max-w-[560px] whitespace-pre-wrap break-words text-[14px] font-bold leading-6 tracking-[0.06em] text-[#80fff2] light:text-[#0b7a6d]">{{ $question['content'] }}</p>
                                        @if($question['image_url'])
                                            <button type="button" class="mx-auto mt-2 block cursor-zoom-in" onclick="openImageModal('{{ $question['image_url'] }}')" aria-label="Perbesar gambar soal">
                                                <img src="{{ $question['image_url'] }}" alt="Gambar soal" class="max-h-[150px] border border-[#3b494b]">
                                            </button>
                                        @endif
                                        <p class="mt-2 text-[10px] tracking-[0.4em] text-gray-500">•••</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- KARTU JAWABAN: label + ikon DI DALAM kontainer (gaya label, bukan header) --}}
                        <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                            <div class="flex flex-col gap-2.5 px-5 py-3.5">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.14em] text-violet-400 light:text-violet-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/></svg>
                                        <span>JAWABAN</span>
                                    </div>
                                    <span class="question-badge {{ $statusLabel ? '' : 'hidden' }} border px-1.5 py-0.5 text-[8px] font-bold uppercase {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>

                                @if($isPg)
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        @php $optionCount = count($question['options'] ?? []); @endphp
                                        @foreach(($question['options'] ?? []) as $key => $text)
                                            <label class="flex cursor-pointer items-center border border-[#3b494b] bg-[#0f1017] px-3 py-2 text-[11px] text-gray-200 transition hover:border-[#30A192] has-[:checked]:border-[#38bdf8] has-[:checked]:bg-[#bae6fd] has-[:checked]:text-[#0c4a6e] light:bg-[#ECECEC] light:text-[#003033] light:has-[:checked]:bg-[#bae6fd] light:has-[:checked]:text-[#0c4a6e] {{ $loop->last && $optionCount % 2 === 1 ? 'sm:col-span-2 sm:mx-auto sm:w-1/2' : '' }} {{ $isFinal ? 'pointer-events-none opacity-70' : '' }}">
                                                <input type="radio" name="answer_{{ $question['id'] }}" value="{{ $key }}"
                                                       @checked($question['answer'] === $key)
                                                       @disabled($isFinal)
                                                       class="sr-only">
                                                <span class="font-bold uppercase">{{ $key }}.</span>
                                                <span class="ml-2">{{ $text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div>
                                        <textarea rows="2" id="answer-{{ $question['id'] }}"
                                                  placeholder="FLAG{...}"
                                                  @disabled($isFinal)
                                                  class="w-full resize-none border border-[#3b494b] bg-[#0f1017] px-3 py-2 text-[11px] tracking-[0.04em] text-gray-200 outline-none placeholder:text-gray-500 disabled:opacity-70 light:bg-[#ECECEC] light:text-[#003033] light:placeholder:text-gray-500 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">{{ $question['answer'] }}</textarea>
                                        <p class="answer-note mt-1.5 hidden text-right text-[10px] font-bold text-[#ff9a9e] light:text-red-700"></p>
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- Tombol kirim: elemen TERPISAH di bawah kontainer, memanjang sejajar kontainer. --}}
                        @if(! $isFinal)
                            <button type="button" class="answer-footer flex h-9 w-full items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] hover:bg-[#174b96]"
                                    onclick="submitQuestion({{ $question['id'] }}, currentAnswer({{ $question['id'] }}))">KIRIM JAWABAN</button>
                        @endif
                    </div>
                @endforeach

                {{-- Tombol global: muncul menggantikan KIRIM JAWABAN ketika SEMUA soal sudah terjawab. --}}
                <button type="button" id="finishGlobalButton" onclick="finishAttempt()"
                        class="hidden h-9 w-full items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] hover:bg-[#174b96]">SELESAI</button>
            </div>

            {{-- ===================== KOLOM KANAN: SIDEBAR STATUS =====================
                 Mengikuti tinggi konten; navigator soal menyesuaikan jumlah soal. --}}
            <aside class="flex min-w-0 flex-col gap-2.5 lg:sticky lg:top-3">

                {{-- BATAS WAKTU --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.12em] text-amber-300 light:text-amber-700">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Batas_Waktu</span>
                    </div>
                    <div class="mt-1.5 flex items-center justify-between text-[10px]">
                        <span class="text-gray-400 light:text-gray-600">Waktu</span>
                        <span id="timerLabel" class="text-[11px] font-bold text-gray-200 light:text-[#003033]">--:--</span>
                    </div>
                    <div class="mt-1 flex items-center justify-between text-[10px]">
                        <span class="text-gray-400 light:text-gray-600">TINGKAT</span>
                        <span id="sideDifficulty" class="font-bold uppercase text-gray-200 light:text-[#003033]">{{ strtoupper((string) ($questions[0]['difficulty'] ?? '')) }}</span>
                    </div>
                </section>

                {{-- STATUS PENGERJAAN --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center justify-center gap-2 text-[9px] font-bold uppercase tracking-[0.12em] text-[#4f8cff] light:text-[#1d4ed8]">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z"/></svg>
                        <span>STATUS_PENGERJAAN</span>
                    </div>
                    <p class="mt-1.5 text-center text-[18px] font-bold leading-none text-gray-200 light:text-[#003033]">
                        <span id="sideFinalized">{{ $finalizedCount }}</span><span class="text-gray-500">/</span><span id="sideTotal">{{ $totalQuestions }}</span>
                    </p>
                    <div class="mt-1.5 h-1 w-full overflow-hidden rounded-full bg-[#2a2d31] light:bg-[#d1d5db]">
                        <div id="sideProgressBar" class="h-full rounded-full bg-[#20c5e2] transition-all" style="width: {{ $totalQuestions > 0 ? round($finalizedCount / $totalQuestions * 100) : 0 }}%"></div>
                    </div>
                </section>

                {{-- REWARD --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center justify-between">
                        <span class="text-[9px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">REWARD</span>
                        <svg class="h-3.5 w-3.5 text-violet-400 light:text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2l8 4.5v9L12 20l-8-4.5v-9L12 2z"/></svg>
                    </div>
                    <p class="mt-0.5 text-[14px] font-bold leading-none text-violet-400 light:text-violet-600"><span id="sideXp">{{ number_format($xp, 0) }}</span> XP</p>
                    <div class="mt-1.5 flex items-center justify-between gap-3">
                        <div id="sideXpBar" class="flex gap-1">
                            @for($seg = 1; $seg <= 10; $seg++)
                                <span class="xp-seg h-2.5 w-1.5 {{ $seg <= round($progress / 10) ? 'bg-violet-500' : 'bg-[#3a3a3a] light:bg-[#c4c4c4]' }}"></span>
                            @endfor
                        </div>
                        <div class="text-right text-[8px] uppercase leading-3 text-gray-400 light:text-gray-600">
                            <div>LEVEL PROGRESS: <span id="sideLevelProgress">{{ number_format((float) $progress, 0) }}%</span></div>
                            <div>RANK <span id="sideRank">{{ $rank }}</span>/<span id="sideParticipants">{{ $totalParticipants }}</span> PESERTA</div>
                        </div>
                    </div>
                </section>

                {{-- NAVIGATOR SOAL: semua nomor tampil sekaligus (tanpa scroll internal) --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex flex-wrap items-center justify-center gap-3 text-[8px] uppercase text-gray-400 light:text-gray-600">
                        <span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-[#d4d4d4] light:bg-[#d9d9d9]"></span> Belum dikerjakan</span>
                        <span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-[#4caf50]"></span> Selesai dikerjakan</span>
                    </div>
                    <div id="questionNav" class="mt-2 grid grid-cols-7 gap-1">
                        @foreach($questions as $index => $question)
                            <button type="button" data-nav-index="{{ $index }}"
                                    onclick="showQuestion({{ $index }})"
                                    class="nav-btn flex aspect-square items-center justify-center border text-[10px] font-bold transition
                                        {{ $question['final'] ? 'border-[#4caf50] bg-[#4caf50] text-[#061015]' : 'border-[#3b494b] bg-[#d4d4d4] text-[#0d0e12] light:bg-[#d9d9d9]' }}
                                        {{ $index === 0 ? 'ring-2 ring-[#20c5e2]' : '' }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </section>

                {{-- HINT SYSTEM --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.12em] text-[#4f8cff] light:text-[#1d4ed8]">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.355-1.754-.988-2.386l-.548-.547z"/></svg>
                        <span>HINT_SYSTEM</span>
                    </div>
                    <p class="mt-1 text-[9px] italic leading-3 text-gray-400 light:text-gray-600">Butuh bantuan? Membuka petunjuk akan memotong skor akhir Anda.</p>
                    <button type="button" id="hintButton" onclick="openHint()"
                            class="mt-1.5 flex h-7 w-full items-center justify-center gap-2 border border-[#4f8cff] text-[9px] font-bold uppercase tracking-[0.12em] text-[#8fb8ff] transition hover:bg-[#123b78]/40 light:text-[#1d4ed8]">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="5" y="11" width="14" height="9" rx="1.5" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M8 11V8a4 4 0 118 0v3"/></svg>
                        <span id="hintButtonLabel">BUKA HINT (-PTS)</span>
                    </button>
                </section>
            </aside>
        </div>
    </div>
    @endcan

    {{-- MODAL HINT: SUBSISTEM_PETUNJUK --}}
    <div id="hintModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 px-4">
        <div class="w-full max-w-[480px] border border-[#4f8cff] bg-[#202020] shadow-[0_0_40px_rgba(79,140,255,0.35)] light:bg-white">
            {{-- Garis atas popup: light = warna header (#003033); dark = gradien hitam → putih (tengah) → hitam --}}
            <div class="h-[3px] w-full bg-gradient-to-r from-black via-white to-black light:bg-none light:bg-[#003033]"></div>
            <h3 class="px-6 pt-5 text-[15px] font-bold uppercase tracking-tight text-gray-100 light:text-[#003033]">SUBSISTEM_PETUNJUK</h3>
            <div class="px-6 pb-4 pt-3">
                <div class="border border-[#3b494b] px-3.5 py-3">
                    <p class="text-[9px] font-bold uppercase tracking-[0.14em] text-[#8fb8ff] light:text-[#1d4ed8]">HINT_01: ANALISIS SOAL</p>
                    <p id="hintModalText" class="mt-1.5 text-[11px] leading-4 text-gray-300 light:text-[#1a2e28]"></p>
                </div>
                <div class="mt-4 flex justify-center">
                    <button type="button" onclick="closeHintModal()"
                            class="h-8 w-[230px] border border-[#4f8cff] text-[9px] font-bold uppercase tracking-[0.14em] text-[#8fb8ff] transition hover:bg-[#123b78]/40 light:text-[#1d4ed8]">KEMBALI</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL GAMBAR SOAL: zoom dengan scroll & tombol zoom in/out --}}
    <div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 px-4">
        <div class="flex h-full w-full max-w-[900px] flex-col py-6">
            <div class="flex items-center justify-between pb-3">
                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200">GAMBAR SOAL</span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="zoomImage(-0.25)" class="flex h-7 w-7 items-center justify-center border border-[#4f8cff] text-[13px] font-bold text-[#8fb8ff] hover:bg-[#123b78]/40" aria-label="Perkecil">&#8722;</button>
                    <button type="button" onclick="zoomImage(0.25)" class="flex h-7 w-7 items-center justify-center border border-[#4f8cff] text-[13px] font-bold text-[#8fb8ff] hover:bg-[#123b78]/40" aria-label="Perbesar">+</button>
                    <button type="button" onclick="resetZoomImage()" id="imageZoomLabel" class="flex h-7 items-center justify-center border border-[#4f8cff] px-3 text-[9px] font-bold uppercase tracking-[0.12em] text-[#8fb8ff] hover:bg-[#123b78]/40">100%</button>
                    <button type="button" onclick="closeImageModal()" class="flex h-7 w-7 items-center justify-center border border-red-400/70 text-[13px] font-bold text-red-300 hover:bg-red-500/20" aria-label="Tutup">&times;</button>
                </div>
            </div>
            <div class="flex min-h-0 flex-1 overflow-auto border border-[#3b494b] bg-[#0f1017] p-6">
                <img id="imageModalImg" src="" alt="Gambar soal" class="m-auto block max-w-none select-none" style="width: 100%;">
            </div>
        </div>
    </div>

    <script>
        const app = document.getElementById('attemptApp');
        let remaining = parseInt(app?.dataset.remainingSeconds || '0', 10);
        let finished = false;
        let saveTimer = null;

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
        }

        function showNotice(message, ok = true) {
            const el = document.getElementById('noticeBox');
            if (!el) return;
            el.textContent = message;
            el.className = 'mb-3 border px-4 py-2 text-[11px] ' + (ok
                ? 'border-[#30A192]/50 bg-[#30A192]/10 text-[#80fff2] light:bg-emerald-50 light:text-emerald-700'
                : 'border-amber-400/50 bg-amber-500/10 text-amber-300 light:bg-amber-50 light:text-amber-700');
            el.classList.remove('hidden');
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => el.classList.add('hidden'), 2500);
        }

        // ===== Navigasi satu soal per tampilan =====
        function sections() {
            return Array.from(document.querySelectorAll('.attempt-question'));
        }

        function activeSection() {
            return document.querySelector('.attempt-question:not(.hidden)');
        }

        function showQuestion(index) {
            sections().forEach((section) => {
                const isTarget = parseInt(section.dataset.index, 10) === index;
                section.classList.toggle('hidden', !isTarget);
                section.classList.toggle('flex', isTarget);
            });
            updateSidebar();
        }

        // Lanjut otomatis ke soal berikutnya yang belum terjawab (global: PG & uraian).
        // Cari setelah index aktif; bila tidak ada, cari dari awal (dinamis).
        function goToNextQuestion(fromIndex) {
            const all = sections();

            let next = all.find((section) => parseInt(section.dataset.index, 10) > fromIndex && section.dataset.final !== '1');

            if (!next) {
                next = all.find((section) => section.dataset.final !== '1');
            }

            if (next) {
                showQuestion(parseInt(next.dataset.index, 10));
            } else {
                // Semua sudah terjawab: tombol global berubah jadi SELESAI.
                updateSidebar();
            }
        }

        function badgeFor(section, status) {
            const badge = section.querySelector('.question-badge');
            if (!badge) return;

            // Hanya WAKTU HABIS yang ditampilkan; benar/salah dirahasiakan.
            const map = {
                time_expired: ['WAKTU HABIS', 'border-gray-500 bg-gray-500/10 text-gray-400'],
                in_progress: ['', ''],
                correct: ['', ''],
                wrong: ['', ''],
            };

            const [label, cls] = map[status] ?? ['', ''];

            if (!label) { badge.classList.add('hidden'); return; }
            badge.textContent = label;
            badge.className = 'question-badge border px-1.5 py-0.5 text-[8px] font-bold uppercase ' + cls;
        }

        // Pesan di bawah textarea jawaban uraian: hanya saat jawaban masih salah.
        function showAnswerNote(section, message) {
            const el = section.querySelector('.answer-note');
            if (!el) return;

            if (!message) {
                el.textContent = '';
                el.classList.add('hidden');
                return;
            }

            el.textContent = message;
            el.classList.remove('hidden');
        }

        // ===== Sidebar live: status, XP, progress, navigator, tingkat, hint =====
        function updateSidebar() {
            const all = sections();
            const total = all.length;
            const finalized = all.filter((section) => section.dataset.final === '1').length;
            const xp = all.reduce((sum, section) => sum + (parseFloat(section.dataset.score || '0') || 0), 0);

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            setText('sideFinalized', finalized);
            setText('sideTotal', total);
            setText('sideXp', Math.round(xp));

            const bar = document.getElementById('sideProgressBar');
            if (bar) bar.style.width = total > 0 ? Math.round(finalized / total * 100) + '%' : '0%';

            // Navigator: hijau = selesai, abu = belum; ring = soal aktif.
            all.forEach((section) => {
                const btn = document.querySelector(`[data-nav-index="${section.dataset.index}"]`);
                if (!btn) return;

                const done = section.dataset.final === '1';
                btn.className = 'nav-btn flex aspect-square items-center justify-center border text-[10px] font-bold transition '
                    + (done
                        ? 'border-[#4caf50] bg-[#4caf50] text-[#061015]'
                        : 'border-[#3b494b] bg-[#d4d4d4] text-[#0d0e12] light:bg-[#d9d9d9]');

                if (section === activeSection()) btn.classList.add('ring-2', 'ring-[#20c5e2]');
            });

            // Info soal aktif: tingkat + tombol hint.
            const active = activeSection();
            if (!active) return;

            setText('sideDifficulty', active.dataset.difficultyLabel || '');

            const hintButton = document.getElementById('hintButton');
            const hintLabel = document.getElementById('hintButtonLabel');
            const hasHint = active.dataset.hasHint === '1';

            if (hintButton && hintLabel) {
                hintButton.disabled = !hasHint;
                hintButton.classList.toggle('opacity-40', !hasHint);
                hintLabel.textContent = active.dataset.hintOpened === '1' ? 'LIHAT HINT' : 'BUKA HINT (-PTS)';
            }

            // Tombol global SELESAI: tampil HANYA bila semua soal sudah terjawab
            // (menyembunyikan semua tombol KIRIM JAWABAN yang tersisa).
            const allDone = total > 0 && finalized === total;
            const finishButton = document.getElementById('finishGlobalButton');
            if (finishButton) finishButton.classList.toggle('hidden', !allDone);
        }

        function updateRewardProgress(attemptScore) {
            const value = Math.max(0, Math.min(100, parseFloat(attemptScore) || 0));

            const progressEl = document.getElementById('sideLevelProgress');
            if (progressEl) progressEl.textContent = Math.round(value) + '%';

            const segments = document.querySelectorAll('#sideXpBar .xp-seg');
            const filled = Math.round(value / 10);
            segments.forEach((seg, i) => {
                seg.className = 'xp-seg h-2.5 w-1.5 ' + (i < filled ? 'bg-violet-500' : 'bg-[#3a3a3a] light:bg-[#c4c4c4]');
            });
        }

        async function postJson(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify(payload ?? {}),
            });

            const data = await response.json().catch(() => ({}));
            return { ok: response.ok, data };
        }

        // Ambil jawaban aktif pada satu soal (radio PG atau textarea uraian).
        function currentAnswer(attemptQuestionId) {
            const section = document.querySelector(`[data-attempt-question-id="${attemptQuestionId}"]`);
            if (!section) return '';

            const checked = section.querySelector('input[type="radio"]:checked');
            if (checked) return checked.value;

            const textarea = section.querySelector('textarea');
            return textarea ? textarea.value : '';
        }

        async function submitQuestion(attemptQuestionId, answer) {
            if (finished || answer === null || answer === '') return;

            const section = document.querySelector(`[data-attempt-question-id="${attemptQuestionId}"]`);
            if (!section || section.dataset.final === '1') return;

            const { ok, data } = await postJson(app.dataset.submitUrl, {
                attempt_question_id: attemptQuestionId,
                answer,
            });

            if (!ok || !data.success) {
                showNotice(data.error || 'Gagal mengirim jawaban.', false);
                return;
            }

            if (data.final) {
                section.dataset.final = '1';
                section.dataset.status = data.status;
                section.dataset.score = Number(data.score).toFixed(2);
                section.querySelectorAll('input, textarea, button').forEach((el) => {
                    el.disabled = true;
                });

                // Sembunyikan baris tombol kirim setelah soal final.
                section.querySelector('.answer-footer')?.classList.add('hidden');

                const isPg = section.dataset.isPg === '1';

                if (isPg) {
                    // PG: benar/salah dirahasiakan (tanpa badge & notifikasi) —
                    // petunjuknya hanya dari perubahan reward XP.
                    updateRewardProgress(data.attempt_score);
                    updateSidebar();
                    goToNextQuestion(parseInt(section.dataset.index, 10));
                    return;
                }

                // Uraian final (kemiripan >= 75%): tanpa notifikasi benar/salah.
                // Hapus pesan salah dari percobaan sebelumnya.
                showAnswerNote(section, '');

                if (data.time_expired) {
                    badgeFor(section, data.status);
                }

                updateRewardProgress(data.attempt_score);
                updateSidebar();

                // Sama seperti PG: langsung lanjut ke soal berikutnya yang belum terjawab.
                goToNextQuestion(parseInt(section.dataset.index, 10));
                return;
            } else {
                // Uraian belum final (kemiripan < 75%): pesan di bawah textarea.
                showAnswerNote(section, 'Jawaban masih salah, coba ulangi lagi');

                section.dataset.wrongCount = data.wrong_count;
            }

            updateSidebar();
        }

        // ===== Hint via modal SUBSISTEM_PETUNJUK (penalti 2% konstanta) =====
        function showHintModal(text) {
            const modal = document.getElementById('hintModal');
            const body = document.getElementById('hintModalText');
            if (body) body.textContent = text;
            modal?.classList.replace('hidden', 'flex');
        }

        function closeHintModal() {
            const modal = document.getElementById('hintModal');
            modal?.classList.replace('flex', 'hidden');
        }

        async function openHint() {
            if (finished) return;

            const section = activeSection();
            if (!section || section.dataset.hasHint !== '1') return;

            // Sudah pernah dibuka: tampilkan langsung tanpa memanggil API lagi.
            if (section.dataset.hintOpened === '1') {
                showHintModal(section.dataset.hint || '');
                return;
            }

            const url = app.dataset.hintUrlTemplate.replace('__AQ__', section.dataset.attemptQuestionId);
            const { ok, data } = await postJson(url);

            if (!ok || !data.success) {
                showNotice(data.error || 'Gagal membuka hint.', false);
                return;
            }

            section.dataset.hintOpened = '1';
            section.dataset.hint = data.hint;
            showHintModal(data.hint);
            updateSidebar();
        }

        document.getElementById('hintModal')?.addEventListener('click', (event) => {
            if (event.target === event.currentTarget) closeHintModal();
        });

        // ===== Modal gambar soal (zoom) =====
        let imageZoom = 1;

        function applyImageZoom() {
            const img = document.getElementById('imageModalImg');
            const label = document.getElementById('imageZoomLabel');
            if (!img) return;

            img.style.width = Math.round(imageZoom * 100) + '%';
            img.style.maxWidth = 'none';
            if (label) label.textContent = Math.round(imageZoom * 100) + '%';
        }

        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('imageModalImg');
            if (!modal || !img) return;

            img.src = src;
            imageZoom = 1;
            applyImageZoom();
            modal.classList.replace('hidden', 'flex');
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('imageModalImg');
            if (!modal) return;

            modal.classList.replace('flex', 'hidden');
            if (img) img.src = '';
            imageZoom = 1;
        }

        function zoomImage(step) {
            imageZoom = Math.min(4, Math.max(0.25, imageZoom + step));
            applyImageZoom();
        }

        function resetZoomImage() {
            imageZoom = 1;
            applyImageZoom();
        }

        document.getElementById('imageModal')?.addEventListener('click', (event) => {
            if (event.target === event.currentTarget) closeImageModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeImageModal();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeHintModal();
        });

        // ===== Selesai / timer (mekanik tidak berubah) =====
        function submitFinishForm(reason) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = app.dataset.finishUrl;

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = csrfToken();
            form.appendChild(token);

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        }

        // Tombol global SELESAI (muncul saat semua soal sudah terjawab).
        // Tanpa konfirmasi: langsung menyelesaikan challenge.
        function finishAttempt() {
            if (finished) return;
            finished = true;
            submitFinishForm('manual');
        }

        function updateTimer() {
            const label = document.getElementById('timerLabel');
            if (!label) return;

            if (remaining <= 0) {
                label.textContent = '00:00';
                if (!finished) {
                    finished = true;
                    showNotice('Waktu habis — mengirim jawaban...', false);
                    submitFinishForm('time_expired');
                }
                return;
            }

            remaining -= 1;
            const m = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            label.textContent = `${m}:${s}`;
        }

        if (app) {
            const timerLabel = document.getElementById('timerLabel');
            if (timerLabel && remaining > 0) {
                const m = String(Math.floor(remaining / 60)).padStart(2, '0');
                const s = String(remaining % 60).padStart(2, '0');
                timerLabel.textContent = `${m}:${s}`;
            }
            setInterval(updateTimer, 1000);
            updateSidebar();
        }
    </script>
</x-app-layout>
