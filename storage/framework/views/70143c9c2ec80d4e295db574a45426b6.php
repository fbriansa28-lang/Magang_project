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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">LOG SISTEM</h2> <?php $__env->endSlot(); ?>

    <div class="mx-auto w-full max-w-[1080px] min-w-0 font-mono">
        <section class="overflow-hidden border border-[#263239] bg-black light:border-[#b8b8b8] light:bg-white">
            
            <div class="flex h-10 items-center gap-2 border-b border-[#3b494b] bg-[#25272a] px-3 light:border-[#003033] light:bg-[#003033]">
                <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>
                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                <span class="ml-1 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-200 light:text-white">LOG_SISTEM</span>
            </div>

            
            <form method="GET" action="<?php echo e(route('activity-log.index')); ?>" class="flex min-h-[52px] flex-wrap items-center gap-2 border-b border-[#3b494b] bg-[#161822] px-3 py-2 light:border-[#d1d5db] light:bg-[#9CA3AF]">
                <div class="flex h-8 min-w-[190px] flex-1 items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white">
                    <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/></svg>
                    <input name="search" value="<?php echo e($filters['search'] ?? ''); ?>" type="search" placeholder="CARI..." class="min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] uppercase tracking-wide text-gray-200 outline-none placeholder:text-gray-500 focus:ring-0 light:text-[#003033]">
                </div>

                <?php if($canViewAll): ?>
                    <select name="user_id" onchange="this.form.submit()" class="h-8 min-w-[130px] border border-[#3b494b] bg-[#0f1017] px-2 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]">
                        <option value="">PENGGUNA</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php if((string) ($filters['user_id'] ?? '') === (string) $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                <?php endif; ?>

                <select name="action" onchange="this.form.submit()" class="h-8 min-w-[150px] border border-[#3b494b] bg-[#0f1017] px-2 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]">
                    <option value="">AKSI</option>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(($filters['action'] ?? '') === $key): echo 'selected'; endif; ?>><?php echo e(strtoupper(str_replace('_', ' ', $key))); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <div class="flex h-8 items-center gap-1 border border-[#3b494b] bg-[#0f1017] px-2 light:bg-white">
                    <span class="shrink-0 text-[9px] uppercase tracking-wide text-gray-400 light:text-[#003033]">RENTANG</span>
                    <input type="date" name="date_from" value="<?php echo e($filters['date_from'] ?? ''); ?>" onchange="this.form.submit()" aria-label="Tanggal mulai" class="h-6 min-w-0 border-0 bg-transparent px-1 text-[10px] text-gray-200 outline-none light:text-[#003033]">
                    <span class="text-[10px] text-gray-500">-</span>
                    <input type="date" name="date_to" value="<?php echo e($filters['date_to'] ?? ''); ?>" onchange="this.form.submit()" aria-label="Tanggal akhir" class="h-6 min-w-0 border-0 bg-transparent px-1 text-[10px] text-gray-200 outline-none light:text-[#003033]">
                </div>

                <?php if(filled($filters['search'] ?? null) || filled($filters['user_id'] ?? null) || filled($filters['action'] ?? null) || filled($filters['date_from'] ?? null) || filled($filters['date_to'] ?? null)): ?>
                    <a href="<?php echo e(route('activity-log.index')); ?>" aria-label="Reset filter" class="flex h-8 w-8 items-center justify-center border border-[#3b494b] text-[13px] font-bold text-gray-300 hover:border-[#30A192] hover:text-[#30A192] light:border-[#003033] light:bg-white light:text-[#003033]">X</a>
                <?php endif; ?>
            </form>

            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] border-collapse">
                    <thead>
                        <tr class="border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]">
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Timestamp</th>
                            <?php if($canViewAll): ?>
                                <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Pengguna</th>
                            <?php endif; ?>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Aksi</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Target</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#003033]">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $statusClass = match ($log->status_key) {
                                    'success' => 'bg-[#1e3a2f] text-[#4ade80] light:bg-emerald-100 light:text-emerald-700',
                                    'critical' => 'bg-[#3a1e20] text-[#ff3038] light:bg-red-100 light:text-red-700',
                                    default => 'bg-[#2a2d31] text-gray-400 light:bg-gray-200 light:text-gray-600',
                                };
                            ?>
                            <tr class="border-b border-[#20272a] bg-[#0d0e12] hover:bg-white/[0.03] light:border-gray-200 light:bg-white light:hover:bg-gray-50">
                                <td class="px-4 py-2.5 text-center text-[10px] text-gray-400 light:text-[#003033]"><?php echo e($log->created_at?->format('Y-m-d H:i:s') ?? '-'); ?></td>
                                <?php if($canViewAll): ?>
                                    <td class="px-4 py-2.5 text-center text-[10px] font-bold uppercase text-gray-200 light:text-[#003033]"><?php echo e($log->user?->name ?? 'SISTEM'); ?></td>
                                <?php endif; ?>
                                <td class="px-4 py-2.5 text-center text-[10px] uppercase text-gray-300 light:text-[#003033]"><?php echo e(strtoupper($log->action)); ?></td>
                                <td class="px-4 py-2.5 text-center text-[10px] text-gray-400 light:text-[#003033]"><?php echo e($log->description ?: ($log->challenge?->title ?? '-')); ?></td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-bold uppercase <?php echo e($statusClass); ?>"><?php echo e($log->status_label); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="<?php echo e($canViewAll ? 5 : 4); ?>" class="px-6 py-14 text-center text-[11px] text-gray-500">Belum ada log aktivitas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <?php
                $pageUrl = fn ($p) => $logs->url($p);
            ?>
            <div class="flex items-center justify-between flex-wrap gap-2 border-t border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
                <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                    Total_Data: <?php echo e(number_format($logs->total())); ?>

                    &nbsp;·&nbsp;
                    Menampilkan: <?php echo e(str_pad($logs->firstItem() ?? 0, 2, '0', STR_PAD_LEFT)); ?>-<?php echo e(str_pad($logs->lastItem() ?? 0, 2, '0', STR_PAD_LEFT)); ?>

                </span>
                <div class="flex items-center gap-1">
                    <a href="<?php echo e($logs->onFirstPage() ? '#' : $pageUrl($logs->currentPage() - 1)); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition <?php echo e($logs->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>">&lsaquo;</a>
                    <?php
                        $start = max(1, min($logs->currentPage() - 1, $logs->lastPage() - 2));
                        $end = min($logs->lastPage(), $start + 2);
                    ?>
                    <?php for($p = $start; $p <= $end; $p++): ?>
                        <a href="<?php echo e($pageUrl($p)); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition <?php echo e($p === $logs->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>"><?php echo e(str_pad($p, 2, '0', STR_PAD_LEFT)); ?></a>
                    <?php endfor; ?>
                    <a href="<?php echo e($logs->hasMorePages() ? $pageUrl($logs->currentPage() + 1) : '#'); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition <?php echo e($logs->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>">&rsaquo;</a>
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
<?php /**PATH D:\laragon\www\ctf_project\resources\views/logactivity/index.blade.php ENDPATH**/ ?>