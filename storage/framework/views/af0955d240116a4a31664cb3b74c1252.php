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
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#003033]">DASHBOARD CHALLENGE</h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-[1180px] mx-auto flex flex-col gap-3.5 font-mono">
        <section class="grid grid-cols-1 md:grid-cols-3 gap-3.5">            <?php $__currentLoopData = [
                ['label' => 'TOTAL BELAJAR', 'value' => $totalLearning, 'color' => 'text-fuchsia-400', 'icon' => 'text-fuchsia-400', 'bg' => 'bg-fuchsia-400/15', 'border' => 'border-fuchsia-400', 'break' => true],
                ['label' => 'BELAJAR TERSELESAIKAN', 'value' => $completedLearning, 'color' => 'text-emerald-400', 'icon' => 'text-emerald-400', 'bg' => 'bg-emerald-400/15', 'border' => 'border-emerald-400'],
                ['label' => 'BELAJAR BELUM TERSELESAIKAN', 'value' => $notCompletedLearning, 'color' => 'text-amber-400', 'icon' => 'text-amber-400', 'bg' => 'bg-amber-400/15', 'border' => 'border-amber-400'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="min-h-[104px] rounded-lg border-l-4 <?php echo e($stat['border']); ?> <?php echo e($stat['color']); ?> bg-[#161822] light:bg-white px-3 py-3.5 flex items-center gap-3.5 shadow-sm">
                    <span class="w-11 h-11 shrink-0 rounded-full <?php echo e($stat['bg']); ?> <?php echo e($stat['icon']); ?> flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="2.5" stroke-width="1.8"/><circle cx="5" cy="7" r="2" stroke-width="1.8"/><circle cx="19" cy="7" r="2" stroke-width="1.8"/><circle cx="5" cy="17" r="2" stroke-width="1.8"/><circle cx="19" cy="17" r="2" stroke-width="1.8"/><path stroke-width="1.8" d="m7 8.5 3.2 2.2m3.6 0L17 8.5M7 15.5l3.2-2.2m3.6 0 3.2 2.2"/>
                        </svg>
                    </span>
                    <div>
                        <div class="max-w-[170px] text-[10px] font-bold leading-3 tracking-[0.12em] text-gray-300 light:text-[#1a2e28]"><?php echo $stat['break'] ?? false ? 'TOTAL<br>BELAJAR' : $stat['label']; ?></div>
                        <div class="text-2xl leading-none mt-0.5 font-light <?php echo e($stat['color']); ?>"><?php echo e(number_format($stat['value'])); ?></div>
                        <div class="text-[9px] text-gray-500 light:text-gray-400 mt-0.5">↗ +12</div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>

        <section>
            <div class="h-9 flex items-center px-2.5 bg-[#12141c] light:bg-[#003033] text-gray-300 light:text-white text-[10px] font-bold uppercase tracking-[0.12em]">Challenge Terbaru</div>
            <div class="min-h-[190px] bg-[#0f1017] light:bg-[#ECECEC] px-2.5 py-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <?php $__empty_1 = true; $__currentLoopData = $activeChallenges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $challenge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="min-h-[150px] rounded-md border border-[#263239] light:border-[#555] bg-[#161822] light:bg-white px-3 py-3.5 shadow-[0_0_16px_rgba(20,240,230,0.12)] light:shadow-[0_4px_12px_rgba(0,0,0,0.2)] flex flex-col">
                            <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.15em] text-[#14f0e6] light:text-[#003033]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-width="1.8" d="M5 9.5a10 10 0 0 1 14 0M8 12.5a6 6 0 0 1 8 0M11 15.5a2 2 0 0 1 2 0"/>
                                    <circle cx="12" cy="19" r="1" fill="currentColor" stroke="none"/>
                                </svg>
                                <span>Tersedia</span>
                            </div>
                            <h3 class="mt-1.5 text-[13px] font-bold text-gray-200 light:text-[#003033] truncate" title="<?php echo e($challenge->title); ?>"><?php echo e($challenge->title); ?></h3>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="px-1.5 py-0.5 bg-[#252a2d] light:bg-[#292929] text-[8px] uppercase tracking-wide text-gray-400"><?php echo e($challenge->category?->name ?? 'Challenge'); ?></span>
                            </div>
                            <div class="mt-2 pt-2 pb-2 border-b border-[#2a3035] light:border-[#d1d5db] text-[10px] text-gray-400 light:text-[#1a2e28] flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle cx="12" cy="13" r="7" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 13V9m0-5v2m-3-2h6"/>
                                </svg>
                                <span>Durasi: <?php echo e($challenge->duration_minutes ?? 0); ?> Menit</span>
                            </div>
                            <div class="mt-auto pt-2.5 text-right"><a href="<?php echo e(route('attempts.start', $challenge)); ?>" class="inline-flex px-3 py-1.5 rounded bg-[#20c5e2] light:bg-[#2563eb] text-[9px] font-bold text-[#061015] light:text-white uppercase hover:opacity-90">Kerjakan</a></div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="sm:col-span-2 lg:col-span-4 py-10 text-center text-[11px] text-gray-500">Belum ada challenge tersedia.</div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section>
            <div class="h-9 flex items-center justify-between px-2.5 bg-[#12141c] light:bg-[#003033] text-gray-300 light:text-white text-[10px] font-bold uppercase tracking-[0.12em]"><span>Learning Terbaru</span><a href="<?php echo e(route('learning.index')); ?>" class="flex items-center gap-2 font-sans text-[10px] font-medium normal-case tracking-normal text-gray-400 light:text-gray-300 hover:text-white light:hover:text-white transition-colors">Lihat Selengkapnya <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14m-6-6 6 6-6 6"/></svg></a></div>
            <div class="bg-[#0f1017] light:bg-[#ECECEC] px-2.5 py-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <?php $__empty_1 = true; $__currentLoopData = $latestLearning; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $status = $material->my_status === 'completed' ? 'SELESAI' : ($material->my_status === 'in_progress' ? 'BERLANJUT' : 'BARU');
                            $difficultyClass = match ($material->level) {
                                'easy' => 'border-emerald-400/60 text-emerald-400 light:text-emerald-600',
                                'hard' => 'border-rose-400/60 text-rose-400 light:text-rose-600',
                                default => 'border-amber-400/60 text-amber-400 light:text-amber-600',
                            };
                        ?>
                        <article class="min-h-[150px] rounded-md border border-[#263239] light:border-[#555] bg-[#161822] light:bg-white px-3 py-3.5 shadow-[0_0_16px_rgba(20,240,230,0.12)] light:shadow-[0_4px_12px_rgba(0,0,0,0.2)] flex flex-col">
                            <div class="flex items-center gap-2 text-[9px] font-bold uppercase tracking-[0.15em] text-[#14f0e6] light:text-[#003033]">
                                <?php if($material->my_status !== 'completed'): ?>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-width="1.8" d="M5 9.5a10 10 0 0 1 14 0M8 12.5a6 6 0 0 1 8 0M11 15.5a2 2 0 0 1 2 0"/>
                                        <circle cx="12" cy="19" r="1" fill="currentColor" stroke="none"/>
                                    </svg>
                                <?php endif; ?>
                                <?php if($material->my_status === 'completed'): ?>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Selesai"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/></svg>
                                <?php endif; ?>
                                <span><?php echo e($status); ?></span>
                            </div>
                            <h3 class="mt-1.5 text-[13px] font-bold text-gray-200 light:text-[#003033] truncate" title="<?php echo e($material->title); ?>"><?php echo e($material->title); ?></h3>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="px-1.5 py-0.5 bg-[#252a2d] light:bg-[#292929] text-[8px] uppercase tracking-wide text-gray-400"><?php echo e($material->category?->name ?? 'Learning'); ?></span>
                                <span class="px-1.5 py-0.5 border <?php echo e($difficultyClass); ?> text-[8px] uppercase tracking-wide"><?php echo e(strtoupper($material->level_label ?? $material->level ?? 'SEDANG')); ?></span>
                            </div>
                            <div class="mt-auto pt-2.5 border-t border-[#2a3035] light:border-[#d1d5db] text-right"><a href="<?php echo e(route('learning.show', $material)); ?>" class="inline-flex px-3 py-1.5 rounded bg-violet-500 light:bg-violet-600 text-[9px] font-bold text-white uppercase hover:opacity-90">Mulai Belajar</a></div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="sm:col-span-2 lg:col-span-4 py-10 text-center text-[11px] text-gray-500">Belum ada materi belajar.</div>
                    <?php endif; ?>
                </div>
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
<?php /**PATH D:\laragon\www\ctf_project\resources\views/dashboard/peserta.blade.php ENDPATH**/ ?>