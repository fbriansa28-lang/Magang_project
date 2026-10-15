<x-app-layout>
    <x-slot name="header"><h2 class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#003033]">BELAJAR</h2></x-slot>

    <div class="mx-auto w-full max-w-[1080px] min-w-0 font-mono" id="simulationApp"
         data-submit-url="{{ route('learning.simulation.submit', $material) }}"
         data-save-url="{{ route('learning.simulation.save-answer', $material) }}"
         data-riwayat-url="{{ route('learning.riwayat', $material) }}"
         data-total-questions="{{ $material->questions->count() }}">

        <div id="noticeBox" class="mb-3 hidden border px-4 py-2 text-[11px]"></div>

        <div class="grid grid-cols-1 items-start gap-3 lg:grid-cols-[minmax(0,1fr)_290px]">

            {{-- ===================== KOLOM KIRI: SOAL + JAWABAN ===================== --}}
            <div class="flex min-w-0 flex-col gap-3">
                @forelse($material->questions as $index => $question)
                    @php
                        $isPg = $question->isPilihanGanda();
                        $saved = $savedAnswers[$question->id] ?? '';
                        $isDone = filled($saved);
                    @endphp

                    <div class="sim-question {{ $index === 0 ? 'flex' : 'hidden' }} min-w-0 flex-col gap-3"
                         data-question-id="{{ $question->id }}"
                         data-index="{{ $index }}"
                         data-done="{{ $isDone ? '1' : '0' }}">

                        {{-- KARTU SOAL --}}
                        <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                            <div class="relative px-5 pb-4 pt-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <span class="text-[9px] font-bold uppercase tracking-[0.14em] text-[#14f0e6] light:text-[#003033]">SOAL {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="border px-1.5 py-0.5 text-[8px] uppercase tracking-wide {{ $isPg ? 'border-emerald-400/60 text-emerald-400 light:text-emerald-600' : 'border-amber-400/60 text-amber-400 light:text-amber-600' }}">
                                        {{ $isPg ? 'PILIHAN GANDA' : 'URAIAN' }}
                                    </span>
                                </div>

                                {{-- Blok gaya terminal berisi konten soal --}}
                                <div class="mt-3 border border-[#3b494b] bg-[#0f1017] light:bg-[#ECECEC]">
                                    <div class="flex h-6 items-center gap-2 border-b border-[#3b494b] bg-[#25272a] px-3 light:border-[#003033] light:bg-[#003033]">
                                        <span class="h-2 w-2 rounded-full bg-[#ff5f9e]"></span>
                                        <span class="h-2 w-2 rounded-full bg-[#ffd166]"></span>
                                        <span class="h-2 w-2 rounded-full bg-[#2ee6a8]"></span>
                                    </div>
                                    <div class="px-4 py-4 text-center">
                                        <p class="text-[8px] font-bold uppercase tracking-[0.3em] text-gray-500 light:text-gray-600">SIMULASI PEMBELAJARAN</p>
                                        <p class="mx-auto mt-2 max-w-[560px] whitespace-pre-wrap break-words text-[14px] font-bold leading-6 tracking-[0.06em] text-[#80fff2] light:text-[#0b7a6d]">{{ $question->question }}</p>
                                        @if($question->image_url)
                                            <button type="button" class="mx-auto mt-2 block cursor-zoom-in" onclick="openImageModal('{{ $question->image_url }}')" aria-label="Perbesar gambar soal">
                                                <img src="{{ $question->image_url }}" alt="Gambar soal" class="max-h-[150px] border border-[#3b494b]">
                                            </button>
                                        @endif
                                        <p class="mt-2 text-[10px] tracking-[0.4em] text-gray-500">•••</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- KARTU JAWABAN --}}
                        <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                            <div class="flex flex-col gap-2.5 px-5 py-3.5">
                                <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.14em] text-violet-400 light:text-violet-600">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"/></svg>
                                    <span>JAWABAN</span>
                                </div>

                                @if($isPg)
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                        @php $optionCount = count($question->options() ?? []); @endphp
                                        @foreach($question->options() as $key => $text)
                                            <label class="flex cursor-pointer items-center border border-[#3b494b] bg-[#0f1017] px-3 py-2 text-[11px] text-gray-200 transition hover:border-[#30A192] has-[:checked]:border-[#38bdf8] has-[:checked]:bg-[#bae6fd] has-[:checked]:text-[#0c4a6e] light:bg-[#ECECEC] light:text-[#003033] light:has-[:checked]:bg-[#bae6fd] light:has-[:checked]:text-[#0c4a6e] {{ $loop->last && $optionCount % 2 === 1 ? 'sm:col-span-2 sm:mx-auto sm:w-1/2' : '' }}">
                                                <input type="radio" name="sim_answer_{{ $question->id }}" value="{{ $key }}" data-answer @checked($saved === $key) class="sr-only">
                                                <span class="font-bold uppercase">{{ $key }}.</span>
                                                <span class="ml-2">{{ $text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <textarea rows="2" data-answer placeholder="Tulis jawaban disini..." class="w-full resize-none border border-[#3b494b] bg-[#0f1017] px-3 py-2 text-[11px] tracking-[0.04em] text-gray-200 outline-none placeholder:text-gray-500 light:bg-[#ECECEC] light:text-[#003033] light:placeholder:text-gray-500">{{ $saved }}</textarea>
                                @endif
                            </div>
                        </section>

                        {{-- Tombol kirim per soal --}}
                        <button type="button" class="answer-footer {{ $isDone ? 'hidden' : 'flex' }} h-9 w-full items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] hover:bg-[#174b96]"
                                onclick="submitQuestion({{ $question->id }})">KIRIM JAWABAN</button>
                    </div>
                @empty
                    <div class="border border-[#263239] bg-[#1b1d1f] px-6 py-12 text-center text-[11px] text-gray-500 light:border-[#3b494b] light:bg-white">Belum ada soal pada materi ini.</div>
                @endforelse

                {{-- Tombol global: muncul ketika SEMUA soal sudah dikerjakan. --}}
                <button type="button" id="finishGlobalButton" onclick="finishSimulation()"
                        class="hidden h-9 w-full items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] hover:bg-[#174b96]">SELESAI</button>
            </div>

            {{-- ===================== KOLOM KANAN: SIDEBAR STATUS ===================== --}}
            <aside class="flex min-w-0 flex-col gap-2.5 lg:sticky lg:top-3">

                {{-- STATUS PENGERJAAN --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center justify-center gap-2 text-[9px] font-bold uppercase tracking-[0.12em] text-[#4f8cff] light:text-[#1d4ed8]">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z"/></svg>
                        <span>STATUS_PENGERJAAN</span>
                    </div>
                    <p class="mt-1.5 text-center text-[18px] font-bold leading-none text-gray-200 light:text-[#003033]">
                        <span id="sideDone">{{ $material->questions->filter(fn ($q) => filled($savedAnswers[$q->id] ?? ''))->count() }}</span><span class="text-gray-500">/</span><span id="sideTotal">{{ $material->questions->count() }}</span>
                    </p>
                    <div class="mt-1.5 h-1 w-full overflow-hidden rounded-full bg-[#2a2d31] light:bg-[#d1d5db]">
                        <div id="sideProgressBar" class="h-full rounded-full bg-[#20c5e2] transition-all" style="width: 0%"></div>
                    </div>
                </section>

                {{-- MATERI --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="text-[9px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">Materi</div>
                    <p class="mt-1 truncate text-[12px] font-bold text-gray-200 light:text-[#003033]" title="{{ $material->title }}">{{ $material->title }}</p>
                    <div class="mt-1.5 flex flex-wrap items-center gap-2 text-[8px] uppercase tracking-wide">
                        <span class="bg-[#252a2d] px-1.5 py-0.5 text-gray-400 light:bg-[#292929]">{{ $material->category?->name ?? 'Learning' }}</span>
                        <span class="border px-1.5 py-0.5 {{ match ($material->level) { 'easy' => 'border-emerald-400/60 text-emerald-400 light:text-emerald-600', 'hard' => 'border-rose-400/60 text-rose-400 light:text-rose-600', default => 'border-amber-400/60 text-amber-400 light:text-amber-600' } }}">{{ $material->level_label ?? strtoupper($material->level) }}</span>
                    </div>
                </section>

                {{-- NAVIGATOR SOAL --}}
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex flex-wrap items-center justify-center gap-3 text-[8px] uppercase text-gray-400 light:text-gray-600">
                        <span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-[#d4d4d4] light:bg-[#d9d9d9]"></span> Belum dikerjakan</span>
                        <span class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-[#4caf50]"></span> Selesai dikerjakan</span>
                    </div>
                    <div id="questionNav" class="mt-2 grid grid-cols-7 gap-1">
                        @foreach($material->questions as $index => $question)
                            @php $done = filled($savedAnswers[$question->id] ?? ''); @endphp
                            <button type="button" data-nav-index="{{ $index }}"
                                    onclick="showQuestion({{ $index }})"
                                    class="nav-btn flex aspect-square items-center justify-center border text-[10px] font-bold transition
                                        {{ $done ? 'border-[#4caf50] bg-[#4caf50] text-[#061015]' : 'border-[#3b494b] bg-[#d4d4d4] text-[#0d0e12] light:bg-[#d9d9d9]' }}
                                        {{ $index === 0 ? 'ring-2 ring-[#20c5e2]' : '' }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>
    </div>

    {{-- MODAL GAMBAR SOAL (zoom) --}}
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
        const simApp = document.getElementById('simulationApp');
        const saveTimers = {};

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
        }

        // ===== Navigasi satu soal per tampilan =====
        function sections() {
            return Array.from(document.querySelectorAll('.sim-question'));
        }

        function showQuestion(index) {
            sections().forEach((section) => {
                const isTarget = parseInt(section.dataset.index, 10) === index;
                section.classList.toggle('hidden', !isTarget);
                section.classList.toggle('flex', isTarget);
            });
            updateSidebar();
        }

        // Lanjut otomatis ke soal berikutnya yang belum dikerjakan.
        function goToNextQuestion(fromIndex) {
            const all = sections();

            let next = all.find((section) => parseInt(section.dataset.index, 10) > fromIndex && section.dataset.done !== '1');

            if (!next) {
                next = all.find((section) => section.dataset.done !== '1');
            }

            if (next) {
                showQuestion(parseInt(next.dataset.index, 10));
            } else {
                updateSidebar();
            }
        }

        function currentAnswer(questionId) {
            const section = document.querySelector(`[data-question-id="${questionId}"]`);
            if (!section) return '';

            const radio = section.querySelector('input[type="radio"][data-answer]:checked');
            if (radio) return radio.value;

            const textarea = section.querySelector('textarea[data-answer]');
            return textarea ? textarea.value : '';
        }

        async function saveDraft(questionId, chosen) {
            try {
                await fetch(simApp.dataset.saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ question_id: questionId, chosen }),
                });
            } catch (error) {
                // Draft gagal tersimpan: abaikan, penilaian tetap terjadi saat SELESAI.
            }
        }

        function markDone(section) {
            section.dataset.done = '1';
            section.querySelector('.answer-footer')?.classList.add('hidden');

            // Kunci jawaban setelah dikirim, seperti pengerjaan challenge.
            section.querySelectorAll('input[type="radio"][data-answer], textarea[data-answer]').forEach((input) => {
                input.disabled = true;
            });

            section.querySelectorAll('label').forEach((label) => {
                label.classList.add('pointer-events-none', 'opacity-70');
            });

            updateSidebar();
        }

        function submitQuestion(questionId) {
            const section = document.querySelector(`[data-question-id="${questionId}"]`);
            if (!section) return;

            const answer = currentAnswer(questionId);

            if (answer === null || answer === '') {
                showNotice('Jawaban masih kosong.', false);
                return;
            }

            saveDraft(questionId, answer);
            markDone(section);
            goToNextQuestion(parseInt(section.dataset.index, 10));
        }

        // ===== Sidebar live: status, navigator, tombol selesai =====
        function updateSidebar() {
            const all = sections();
            const total = all.length;
            const done = all.filter((section) => section.dataset.done === '1').length;

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            setText('sideDone', done);
            setText('sideTotal', total);

            const bar = document.getElementById('sideProgressBar');
            if (bar) bar.style.width = total > 0 ? Math.round(done / total * 100) + '%' : '0%';

            all.forEach((section) => {
                const btn = document.querySelector(`[data-nav-index="${section.dataset.index}"]`);
                if (!btn) return;

                const isDone = section.dataset.done === '1';
                const isActive = !section.classList.contains('hidden');

                btn.className = 'nav-btn flex aspect-square items-center justify-center border text-[10px] font-bold transition '
                    + (isDone
                        ? 'border-[#4caf50] bg-[#4caf50] text-[#061015]'
                        : 'border-[#3b494b] bg-[#d4d4d4] text-[#0d0e12] light:bg-[#d9d9d9]')
                    + (isActive ? ' ring-2 ring-[#20c5e2]' : '');
            });

            const finishBtn = document.getElementById('finishGlobalButton');
            if (finishBtn) {
                const allDone = total > 0 && done === total;
                finishBtn.classList.toggle('hidden', !allDone);
                finishBtn.classList.toggle('flex', allDone);
            }
        }

        // ===== Selesai: kirim semua jawaban untuk dinilai =====
        async function finishSimulation() {
            const answers = sections().map((section) => ({
                question_id: parseInt(section.dataset.questionId, 10),
                chosen: currentAnswer(section.dataset.questionId),
            }));

            if (answers.some((answer) => !answer.chosen)) {
                showNotice('Masih ada soal yang belum dijawab.', false);
                return;
            }

            try {
                const response = await fetch(simApp.dataset.submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ answers }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    showNotice(data.message ?? 'Gagal menilai simulasi.', false);
                    return;
                }

                showNotice(`Selesai! Nilai Anda ${data.correct_count}/${data.total_questions}.`);
                setTimeout(() => window.location.href = simApp.dataset.riwayatUrl, 900);
            } catch (error) {
                showNotice('Terjadi kesalahan saat mengirim jawaban.', false);
            }
        }

        // ===== Aksi jawaban: KIRIM JAWABAN per soal (sama seperti challenge) =====
        sections().forEach((section) => {
            const questionId = parseInt(section.dataset.questionId, 10);

            section.querySelectorAll('input[type="radio"][data-answer]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    // Simpan draft saja; soal baru dianggap selesai setelah KIRIM JAWABAN.
                    saveDraft(questionId, radio.value);
                });
            });

            const textarea = section.querySelector('textarea[data-answer]');
            if (textarea) {
                textarea.addEventListener('input', () => {
                    // Jawaban diubah lagi: buka kunci supaya bisa dikirim ulang.
                    if (section.dataset.done === '1') {
                        section.dataset.done = '0';
                        section.querySelector('.answer-footer')?.classList.remove('hidden');
                        updateSidebar();
                    }

                    clearTimeout(saveTimers[questionId]);
                    saveTimers[questionId] = setTimeout(() => saveDraft(questionId, textarea.value), 800);
                });
            }
        });

        updateSidebar();

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
    </script>
</x-app-layout>
