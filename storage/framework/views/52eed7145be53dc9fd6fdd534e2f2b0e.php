<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> <h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">TANTANGAN</h2> <?php $__env->endSlot(); ?>

    <div class="mx-auto w-full max-w-[1080px] min-w-0 font-mono">
        <?php
            $isAdmin = auth()->user()?->can('show leaderboard') || auth()->user()?->hasRole('Admin');
            $totalQuestions = $questions->count();
            $durationText = '-';
            if ($durationSeconds !== null) {
                $m = intdiv($durationSeconds, 60);
                $s = $durationSeconds % 60;
                $durationText = sprintf('%02d:%02d', $m, $s);
            }
            $correctSegments = $totalQuestions > 0
                ? (int) round($correctCount / $totalQuestions * 10)
                : 0;
        ?>

        
        <section class="border border-[#263239] bg-black light:border-[#3b494b] light:bg-[#ECECEC]">
            
            <div class="flex h-7 items-center gap-2 border-b border-[#3b494b] bg-[#25272a] px-3 light:border-[#003033] light:bg-[#003033]">
                <span class="h-2 w-2 rounded-full bg-fuchsia-500"></span>
                <span class="h-2 w-2 rounded-full bg-cyan-400"></span>
                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                <span class="ml-2 text-[9px] font-bold uppercase tracking-[0.14em] text-gray-200 light:text-white">LAPORAN HASIL PENGERJAAN</span>
            </div>

            <?php if(!$isAdmin): ?>
            
            <div class="bg-black px-5 pb-3 pt-6 text-center light:bg-[#ECECEC]">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full border border-[#30A192]/50 bg-[#0f1017] shadow-[0_0_24px_rgba(48,161,146,0.35)] light:bg-[#ECECEC]">
                    <svg class="h-7 w-7 text-gray-100 light:text-[#003033]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9.5 12 1.8 1.8L15 10"/>
                    </svg>
                </span>

                <h1 class="mx-auto mt-3 max-w-[560px] border border-[#30A192] px-4 py-2 text-[17px] font-bold tracking-tight text-gray-100 shadow-[0_0_24px_rgba(48,161,146,0.25)] light:text-[#003033]">Terima Kasih Sudah mengerjakan Challenge ini</h1>
                <p class="mt-2 text-[8px] font-bold uppercase tracking-[0.3em] text-[#30A192]">KONEKSI BERHASIL</p>
            </div>
            <?php endif; ?>

            
            <div class="grid grid-cols-2 gap-2 px-4 pb-4 pt-2 sm:grid-cols-3 lg:grid-cols-5">
                
                <div class="border border-[#263239] bg-[#1b1d1f] px-3 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-1.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V10m6 9V5m6 14v-7"/></svg>
                        PERINGKAT
                    </div>
                    <p class="mt-1 text-[20px] font-bold leading-none text-violet-400 light:text-violet-600">#<?php echo e(str_pad($rank, 2, '0', STR_PAD_LEFT)); ?></p>
                </div>

                
                <div class="border border-[#263239] bg-[#1b1d1f] px-3 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-1.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V10m6 9V5m6 14v-7"/></svg>
                        SKOR
                    </div>
                    <p class="mt-1 text-[20px] font-bold leading-none text-violet-400 light:text-violet-600"><?php echo e(number_format((float) $attempt->total_score, 0)); ?></p>
                </div>

                
                <div class="border border-[#263239] bg-[#1b1d1f] px-3 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-1.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z"/></svg>
                        JUMLAH BENAR
                    </div>
                    <p class="mt-1 text-[20px] font-bold leading-none text-[#30A192]"><?php echo e($correctCount); ?>/<?php echo e($totalQuestions); ?></p>
                    <div class="mt-2 flex gap-1">
                        <?php for($seg = 1; $seg <= 10; $seg++): ?>
                            <span class="h-2 w-2.5 <?php echo e($seg <= $correctSegments ? 'bg-[#30A192]' : 'bg-[#3a3a3a] light:bg-[#c4c4c4]'); ?>"></span>
                        <?php endfor; ?>
                    </div>
                </div>

                
                <div class="border border-[#263239] bg-[#1b1d1f] px-3 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-1.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.355-1.754-.988-2.386l-.548-.547z"/></svg>
                        PENGGUNAAN HINT
                    </div>
                    <p class="mt-1 text-[20px] font-bold leading-none text-[#20c5e2]"><?php echo e($hintCount); ?></p>
                </div>

                
                <div class="border border-[#263239] bg-[#1b1d1f] px-3 py-2.5 light:border-[#3b494b] light:bg-white">
                    <div class="flex items-center gap-1.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2.5 2.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        WAKTU PENGERJAAN
                    </div>
                    <p class="mt-1 flex items-baseline gap-1 text-[20px] font-bold leading-none text-amber-300 light:text-amber-700"><?php echo e($durationText); ?><span class="text-[9px] font-normal">min</span></p>
                    <p class="mt-2 text-[8px] uppercase text-gray-400 light:text-gray-600">Optimal pengerjaan</p>
                </div>
            </div>

            
            <div class="grid grid-cols-1 gap-3 px-4 pb-4 lg:grid-cols-[minmax(0,1fr)_290px]">

            
            <div class="flex min-w-0 flex-col gap-3">

                
                <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#263239] px-4 py-3 light:border-[#3b494b]">
                        <h2 class="text-[15px] font-extrabold uppercase tracking-tight text-[#30A192]"><?php echo e($attempt->challenge->title); ?></h2>
                        <span class="border border-[#3b494b] px-2 py-0.5 text-[8px] font-bold uppercase tracking-[0.12em] text-gray-400 light:text-gray-600"><?php echo e($attempt->challenge->category?->name ?? 'Umum'); ?></span>
                    </div>
                    <p class="px-4 py-3 text-[11px] leading-5 text-gray-300 light:text-[#1a2e28]"><?php echo e($attempt->challenge->description); ?></p>
                </section>

                <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isPg = $question->type === 'pilihan_ganda';
                        $isCorrect = $question->status === 'correct';
                        $isWrong = in_array($question->status, ['wrong', 'time_expired'], true);
                        $sim = $similarities[$question->id] ?? null;
                        $options = $isPg ? ($question->question?->options() ?? []) : [];
                        $correctOption = strtolower((string) ($question->question?->correct_option ?? ''));
                        $chosen = strtolower((string) ($question->answer ?? ''));
                        $levelLabel = match ($question->difficulty) {
                            'easy' => 'MUDAH',
                            'medium' => 'SEDANG',
                            default => 'SULIT',
                        };
                        $levelClass = match ($question->difficulty) {
                            'easy' => 'bg-[#4caf50] text-[#061015]',
                            'medium' => 'bg-[#ffd166] text-[#3a2a00]',
                            default => 'bg-[#f87171] text-[#3a0500]',
                        };
                    ?>

                    
                    <section class="border border-[#263239] bg-[#1b1d1f] light:border-[#3b494b] light:bg-white">
                        
                        <div class="flex flex-wrap items-center justify-end gap-2 px-4 pt-3">
                            <span class="rounded-full px-2.5 py-0.5 text-[8px] font-bold uppercase <?php echo e($levelClass); ?>"><?php echo e($levelLabel); ?></span>
                            <?php if($question->hintUsed()): ?>
                                <span class="border border-amber-400/60 bg-amber-500/10 px-2 py-0.5 text-[8px] font-bold uppercase text-amber-300 light:text-amber-700">HINT</span>
                            <?php endif; ?>
                        </div>

                        <?php if($question->hintUsed()): ?>
                            <p class="px-4 pt-0.5 text-right text-[8px] italic text-amber-300/80 light:text-amber-700">Menggunakan Hint</p>
                        <?php endif; ?>

                        <h3 class="px-4 pb-3 pt-2 text-[12px] font-bold leading-5 text-gray-200 light:text-[#003033]">
                            <?php echo e($question->order_no); ?>. <?php echo e($question->question?->content); ?>

                        </h3>

                        
                        <div class="mx-4 border-t border-[#263239] light:border-[#3b494b]"></div>

                        
                        <div class="px-4 pb-3 pt-3">
                            <p class="mb-1.5 text-[8px] font-bold uppercase tracking-[0.14em] text-gray-500 light:text-gray-600">JAWABAN</p>

                            <?php if($isPg): ?>
                                <div class="flex flex-col gap-1.5">
                                    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $isKeyCorrect = strtolower((string) $key) === $correctOption;
                                            $isKeyChosen = strtolower((string) $key) === $chosen;
                                            // Warna disamakan dengan grid navigator:
                                            // benar = hijau solid, salah = merah solid.
                                            $optClass = 'border-[#3b494b] bg-[#0f1017] text-gray-300 light:bg-[#ECECEC] light:text-[#1a2e28]';
                                            if ($isKeyCorrect) {
                                                $optClass = 'border-[#4caf50] bg-[#4caf50] text-[#061015] font-bold';
                                            } elseif ($isKeyChosen) {
                                                $optClass = 'border-[#ff3038] bg-[#ff3038] text-white font-bold';
                                            }
                                        ?>
                                        <div class="border px-3 py-1.5 text-[11px] <?php echo e($optClass); ?>"><?php echo e($text); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <?php
                                    // Uraian: jawaban benar = hijau, salah = merah (samakan dgn grid).
                                    $answerClass = $isCorrect
                                        ? 'border-[#4caf50] bg-[#4caf50] text-[#061015] font-bold'
                                        : ($isWrong
                                            ? 'border-[#ff3038] bg-[#ff3038] text-white font-bold'
                                            : 'border-[#3b494b] bg-[#0f1017] text-gray-300 light:bg-[#ECECEC] light:text-[#1a2e28]');
                                ?>
                                <div class="border px-3 py-2 text-[11px] <?php echo e($answerClass); ?>"><?php echo e($question->answer ?: '-'); ?></div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="mx-4 border-t border-[#263239] light:border-[#3b494b]"></div>

                        
                        <div class="px-4 py-3 text-[10px] leading-4 text-gray-400 light:text-gray-600">
                            <?php if(! $isPg && $sim !== null): ?>
                                <p>Hasil jawaban memiliki kemiripan similarity sebesar <?php echo e(round($sim * 100)); ?> % sehingga hasil jawaban dianggap <?php echo e($isCorrect ? 'benar' : 'salah'); ?></p>
                            <?php elseif($isPg): ?>
                                <?php if(filled($question->question?->explanation)): ?>
                                    <p>Mengapa jawaban <?php echo e(strtoupper($chosen ?: '-')); ?> <?php echo e($isCorrect ? 'benar' : 'salah'); ?>? <?php echo e($question->question->explanation); ?></p>
                                <?php endif; ?>
                                <?php if($correctOption !== ''): ?>
                                    <p class="mt-1.5">Sehingga jawaban yang benar adalah <?php echo e(strtoupper($correctOption)); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <aside class="flex min-w-0 flex-col gap-3">
                <section class="border border-[#263239] bg-[#1b1d1f] px-3.5 py-3 light:border-[#3b494b] light:bg-white">
                    <div class="flex flex-wrap items-center justify-center gap-3 text-[8px] uppercase text-gray-400 light:text-gray-600">
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-[#ff3038]"></span> Jawaban Salah</span>
                        <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-[#4caf50]"></span> Jawaban Benar</span>
                    </div>

                    <div class="mt-3 grid grid-cols-6 gap-1.5">
                        <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $navClass = match ($question->status) {
                                    'correct' => 'border-[#4caf50] bg-[#4caf50] text-[#061015]',
                                    'wrong', 'time_expired' => 'border-[#ff3038] bg-[#ff3038] text-white',
                                    default => 'border-[#3b494b] bg-[#d4d4d4] text-[#0d0e12] light:bg-[#d9d9d9]',
                                };
                            ?>
                            <span class="flex aspect-square items-center justify-center border text-[10px] font-bold <?php echo e($navClass); ?>"><?php echo e($question->order_no); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>

                <a href="<?php echo e(route('leaderboard.show', $attempt->challenge)); ?>"
                   class="flex h-9 w-full items-center justify-center border border-[#4f8cff] bg-[#123b78] text-[10px] font-bold uppercase tracking-[0.14em] text-[#dbeafe] hover:bg-[#174b96]">LIHAT PAPAN PERINGKAT</a>
            </aside>
            </div>
        </section>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/challenge/result.blade.php ENDPATH**/ ?>