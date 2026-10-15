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
        <h2 class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#1a2e28]">DASHBOARD</h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-[1180px] mx-auto flex flex-col gap-4 font-mono [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3.5">
            <section class="md:col-span-3 rounded-lg bg-[#161822] light:bg-[#9CA3AF] border border-[#1e2130] light:border-[#b8b8b8] overflow-hidden">
                <div class="px-4 py-4 bg-[#0f1017] light:bg-[#9CA3AF]">
                    <div class="min-h-[215px] rounded-lg border border-[#262a3d] light:border-[#d1d5db] px-4 py-4 bg-[#0d0e12] light:bg-white">
                        <div class="flex items-center justify-between mb-3.5">
                            <span class="text-[10px] font-bold uppercase tracking-[0.16em] text-[#d7ffff] light:text-[#00363A]">Sukses Rate Per Kategori</span>
                            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2">
                                <input type="hidden" name="category_id" value="<?php echo e($categoryId); ?>">
                                <input type="hidden" name="challenge_id" value="<?php echo e($challengeId); ?>">
                                <button name="period" value="7_days" class="h-7 px-3 rounded-sm border text-[9px] uppercase tracking-wide <?php echo e($period === '7_days' ? 'border-[#30A192] text-[#30A192]' : 'border-[#3a4246] text-gray-400'); ?>">7_HARI</button>
                                <button name="period" value="actual" class="h-7 px-3 rounded-sm border text-[9px] uppercase tracking-wide <?php echo e($period !== '7_days' ? 'border-[#30A192] text-[#30A192]' : 'border-[#3a4246] text-gray-400'); ?>">AKTUAL</button>
                            </form>
                        </div>
                        <div class="relative pl-36 pr-7">
                            <div class="absolute inset-y-0 left-36 right-7 flex justify-between pointer-events-none">
                                <?php for($grid = 0; $grid <= 4; $grid++): ?>
                                    <span class="border-l border-dashed border-[#30353a] light:border-[#d1d5db]"></span>
                                <?php endfor; ?>
                            </div>
                            <div class="relative flex flex-col gap-2.5">
                                <?php $__empty_1 = true; $__currentLoopData = $moduleActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $width = $maxActivity > 0 ? max(4, ($activity['count'] / $maxActivity) * 100) : 4;
                                        $barColors = ['bg-[#54b948]', 'bg-[#e2c400]', 'bg-[#e2c400]', 'bg-[#c90038]'];
                                        $barColor = $barColors[$loop->index % count($barColors)];
                                    ?>
                                    <div class="grid grid-cols-1 items-center gap-2">
                                        <div class="relative h-[7px] flex items-center">
                                            <span class="absolute right-full w-32 pr-3 text-right text-[9px] font-semibold uppercase tracking-wide text-gray-400 light:text-[#00363A] truncate" title="<?php echo e($activity['name']); ?>"><?php echo e($activity['name']); ?></span>
                                            <div class="h-full <?php echo e($barColor); ?>" style="width: <?php echo e($width); ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="py-10 text-center text-[11px] text-gray-500">Belum ada aktivitas.</div>
                                <?php endif; ?>
                            </div>
                            <div class="flex justify-between mt-2.5 text-[8px] text-gray-500 light:text-[#6b7280]">
                                <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="md:col-span-1 flex flex-col gap-3">
                <!-- TOTAL PENGGUNA: network/hub icon, blue -->
                <div class="flex-1 rounded-lg flex items-center gap-3 bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#b8b8b8] px-3 py-3.5">
                    <span class="w-9 h-9 rounded-full flex items-center justify-center bg-[#38bdf8]/15 text-[#38bdf8]">
<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 21a8 8 0 00-16 0m8-10a4 4 0 100-8 4 4 0 000 8z"/>
                         </svg>
                    </span>
                    <div>
                        <div class="text-[9px] uppercase tracking-[0.12em] text-gray-500 light:text-[#6b7280]">Total Pengguna</div>
                        <div class="text-2xl font-light text-sky-400 light:text-sky-600"><?php echo e(number_format($totalUser)); ?></div>
                    </div>
                </div>

                <!-- TOTAL TANTANGAN: shield icon, orange/amber -->
                <div class="flex-1 rounded-lg flex items-center gap-3 bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#b8b8b8] px-3 py-3.5">
                    <span class="w-9 h-9 rounded-full flex items-center justify-center bg-[#f8b320]/15 text-[#f8b320]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="text-[9px] uppercase tracking-[0.12em] text-gray-500 light:text-[#6b7280]">Total Tantangan</div>
                        <div class="text-2xl font-light text-amber-400 light:text-amber-600"><?php echo e(number_format($totalChallenge)); ?></div>
                    </div>
                </div>

                <!-- TOTAL BELAJAR: book icon, cyan -->
                <div class="flex-1 rounded-lg flex items-center gap-3 bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#b8b8b8] px-3 py-3.5">
                    <span class="w-9 h-9 rounded-full flex items-center justify-center bg-[#14f0e6]/15 text-[#14f0e6]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 4h6a4 4 0 014 4v13a3 3 0 00-3-3H2V4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 4h-6a4 4 0 00-4 4v13a3 3 0 013-3h7V4z"/>
                        </svg>
                    </span>
                    <div>
                        <div class="text-[9px] uppercase tracking-[0.12em] text-gray-500 light:text-[#6b7280]">Total Belajar</div>
                        <div class="text-2xl font-light text-cyan-400 light:text-cyan-600"><?php echo e(number_format($totalLearning)); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <section class="rounded-lg bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#b8b8b8] overflow-visible">
            <div class="h-10 flex items-center justify-between px-3 border-b border-[#1e2130] light:border-[#003033] bg-[#12141c] light:bg-[#003033] overflow-visible">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#ec13b4]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#14f0e6]"></span>
                    <span class="w-2 h-2 rounded-full bg-[#f8b320]"></span>
                    <span class="ml-2 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-300 light:text-white">Top 5 Peserta Tantangan Terbaru</span>
                </div>
                <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2 overflow-visible">
                    <div class="relative">
                        <select name="category_id" onchange="this.form.submit()" class="appearance-none h-8 w-36 pl-2 pr-7 py-0 leading-none bg-[#0f1017] light:bg-white border border-[#262a3d] light:border-[#e5e7eb] text-[9px] uppercase tracking-wide text-gray-300 light:text-[#1a2e28] outline-none">
                            <option value="">KATEGORI</option>
                            <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php if($categoryId == $category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="relative">
                        <select name="challenge_id" onchange="this.form.submit()" class="appearance-none h-8 w-36 pl-2 pr-7 py-0 leading-none bg-[#0f1017] light:bg-white border border-[#262a3d] light:border-[#e5e7eb] text-[9px] uppercase tracking-wide text-gray-300 light:text-[#1a2e28] outline-none">
                            <option value="">TANTANGAN</option>
                            <?php $__currentLoopData = $challengeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $challenge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($challenge->id); ?>" <?php if($challengeId == $challenge->id): echo 'selected'; endif; ?>><?php echo e($challenge->title); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] border-collapse">
                    <thead>
                        <tr class="border-b border-[#1e2130] light:border-[#b8b8b8] bg-[#1a1c28] light:bg-[#f9fafb]">
                            <th class="py-2.5 pl-16 pr-3 text-center text-[9px] uppercase tracking-wider text-gray-500 light:text-[#1a2e28]">Peringkat</th>
                            <th class="px-6 py-2.5 text-left text-[9px] uppercase tracking-wider text-gray-500 light:text-[#1a2e28]">Username</th>
                            <th class="px-3 py-2.5 text-center text-[9px] uppercase tracking-wider text-gray-500 light:text-[#1a2e28]">Score</th>
                            <th class="py-2.5 pl-3 pr-16 text-center text-[9px] uppercase tracking-wider text-gray-500 light:text-[#1a2e28]">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $top5; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $rankTextColor = $row->rank === 1 ? 'text-violet-400' : ($row->rank === 2 ? 'text-cyan-400' : ($row->rank === 3 ? 'text-pink-400' : 'text-slate-200 light:text-[#1a2e28]')); ?>
                            <tr class="border-b border-[#1e2130]/60 light:border-[#e5e7eb] hover:bg-white/[0.03] light:hover:bg-black/[0.03]">
                                <td class="py-2.5 pl-16 pr-3 text-center text-[12px] font-bold <?php echo e($row->rank === 1 ? 'text-violet-400' : ($row->rank === 2 ? 'text-cyan-400' : ($row->rank === 3 ? 'text-pink-400' : 'text-gray-400 light:text-[#6b7280]'))); ?>">
                                    <?php if($row->rank <= 3): ?>
                                        <span class="mr-2 inline-flex gap-1"><?php for($star = 0; $star < 4 - $row->rank; $star++): ?><span>★</span><?php endfor; ?></span>
                                    <?php endif; ?>
                                    <?php echo e(str_pad($row->rank, 2, '0', STR_PAD_LEFT)); ?>

                                </td>
                                <td class="px-6 py-2.5 text-left"><div class="flex items-center gap-2"><span class="w-7 h-7 rounded border border-[#30A192]/40 bg-[#0f1017] light:bg-[#f3f4f6] text-[#30A192] flex items-center justify-center text-[11px] font-bold overflow-hidden"><?php if($row->user?->image_url): ?><img src="<?php echo e($row->user->image_url); ?>" alt="<?php echo e($row->user->username); ?>" class="w-full h-full object-cover"><?php else: ?><?php echo e(strtoupper(substr($row->user->username ?? $row->user->name ?? '-', 0, 1))); ?><?php endif; ?></span><span class="text-[11px] font-semibold <?php echo e($rankTextColor); ?> uppercase"><?php echo e($row->user->username ?? $row->user->name ?? '-'); ?></span></div></td>
                                <td class="px-3 py-2.5 text-center">
                                    <?php $scoreBarColor = $row->rank === 1 ? 'bg-violet-400' : ($row->rank === 2 ? 'bg-cyan-400' : ($row->rank === 3 ? 'bg-pink-400' : 'bg-slate-300')); ?>
                                    <div class="text-[12px] font-bold <?php echo e($rankTextColor); ?>"><?php echo e(number_format($row->average_score, 2)); ?></div>
                                    <div class="h-1 w-24 mx-auto mt-1 bg-[#1e2130] light:bg-[#e5e7eb]"><div class="h-full <?php echo e($scoreBarColor); ?>" style="width: <?php echo e(min(100, max(0, $row->average_score))); ?>%"></div></div>
                                </td>
                                <td class="py-2.5 pl-3 pr-16 text-center text-[12px] font-bold <?php echo e($rankTextColor); ?>"><?php echo e($row->avg_duration !== null ? number_format($row->avg_duration, 2).' min' : '-'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="px-3 py-10 text-center text-[12px] text-gray-500">Belum ada peserta.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="<?php echo e(route('leaderboard.index')); ?>" class="flex h-10 items-center justify-center gap-2 border-t border-[#1e2130] text-[10px] font-bold uppercase tracking-[0.14em] text-[#30A192] hover:bg-white/[0.03] light:border-[#e5e7eb] light:text-[#ec13b4] light:hover:bg-black/[0.03]">
                LIHAT PAPAN PERINGKAT LEBIH BANYAK <span class="text-[13px] leading-none">&#8594;</span>
            </a>
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
<?php endif; ?><?php /**PATH D:\laragon\www\ctf_project\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>