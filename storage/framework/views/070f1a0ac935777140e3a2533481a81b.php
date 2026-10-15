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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">BANK SOAL</h2> <?php $__env->endSlot(); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view bank soal')): ?>
    <div class="mx-auto w-full max-w-[1230px] font-mono text-[12px]">
        <?php $criticalStats = collect($statistics)->filter(fn ($stat) => $stat['critical'] ?? false); ?>
        <?php if($criticalStats->isNotEmpty()): ?>
            <div class="mb-4 flex items-start gap-3 border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-[12px] text-amber-300">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m0 3.75h.007v.008H12v-.008zM21 12a9 9 0 1 1-18 0"/></svg>
                <div>
                    <p class="font-semibold">Stok soal menipis</p>
                    <p class="mt-0.5 text-amber-300/80">
                        <?php $__currentLoopData = $criticalStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($stat['label']); ?> (<?php echo e($stat['total']); ?> soal)<?php echo e(!$loop->last ? ', ' : ''); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        — tambahkan soal agar distribusi otomatis saat challenge dibuat tidak sering fallback.
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <div class="mb-5 grid grid-cols-5 gap-3" style="grid-template-columns:repeat(4,minmax(0,1fr)) 180px;">
            <?php $statCards=[['label'=>'Total Soal','value'=>$totalStatistic['total'],'recent'=>$totalStatistic['recent'],'color'=>'#c86cff'],['label'=>'Total Soal Mudah','value'=>$statistics['easy']['total'],'recent'=>$statistics['easy']['recent'],'color'=>'#30d6a4'],['label'=>'Total Soal Sedang','value'=>$statistics['medium']['total'],'recent'=>$statistics['medium']['recent'],'color'=>'#ffbd19'],['label'=>'Total Soal Sulit','value'=>$statistics['hard']['total'],'recent'=>$statistics['hard']['recent'],'color'=>'#ff3038']]; ?>
            <?php $__currentLoopData = $statCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><section class="h-[105px] border-l-4 bg-[#1b1d1f] px-4 py-3 light:bg-white" style="border-left-color:<?php echo e($stat['color']); ?>"><p class="text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400 light:text-gray-600"><?php echo e($stat['label']); ?></p><p class="mt-1 text-[38px] leading-none" style="color:<?php echo e($stat['color']); ?>"><?php echo e($stat['value']); ?></p><p class="mt-1 text-[10px] font-bold uppercase text-lime-400 light:text-gray-600">+<?php echo e($stat['recent']); ?> Baru</p></section><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create bank soal')): ?><a href="<?php echo e(route('bank-soal.create')); ?>" class="flex h-[32px] items-center justify-center self-end bg-[#30A192] px-3 text-[10px] font-bold uppercase text-[#061015] shadow-lg shadow-[#30A192]/20 hover:opacity-90 light:bg-[#2563eb] light:text-white"><span class="mr-2 text-xl font-light">+</span> Tambah Soal</a><?php endif; ?>
        </div>
        <section class="overflow-hidden">
            <form method="GET" action="<?php echo e(route('bank-soal.index')); ?>" class="flex min-h-[56px] items-center gap-3 bg-[#25272a] px-3 text-[10px] uppercase tracking-[0.14em] text-white light:bg-[#003033]"><div class="flex items-center gap-2 font-bold"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-1">BANK_SOAL // DAFTAR_SOAL</span></div><div class="flex h-8 min-w-[180px] flex-[0.75] items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white"><svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/></svg><input name="search" value="<?php echo e(request('search')); ?>" type="search" placeholder="Search" class="min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] text-gray-200 outline-none placeholder:text-gray-500 focus:ring-0 light:text-[#003033]"></div><select name="category" onchange="this.form.submit()" class="ml-auto h-8 min-w-[150px] border border-[#3b494b] bg-[#0f1017] px-3 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]"><option value="">Kategori</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category->id); ?>" <?php if((string)request('category')===(string)$category->id): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><select name="difficulty" onchange="this.form.submit()" class="h-8 min-w-[105px] border border-[#3b494b] bg-[#0f1017] px-3 py-0 text-[10px] font-bold uppercase leading-none text-gray-200 outline-none light:bg-white light:text-[#003033]"><option value="">Level</option><option value="easy" <?php if(request('difficulty')==='easy'): echo 'selected'; endif; ?>>Mudah</option><option value="medium" <?php if(request('difficulty')==='medium'): echo 'selected'; endif; ?>>Sedang</option><option value="hard" <?php if(request('difficulty')==='hard'): echo 'selected'; endif; ?>>Sulit</option></select></form>
            <div class="space-y-2 bg-[#0d0e12] py-5 light:bg-[#ECECEC]">
                <?php $__empty_1 = true; $__currentLoopData = $questionBanks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="grid min-h-[55px] items-center gap-4 rounded-xl border border-[#3b494b] bg-[#202020] px-5 py-2 light:bg-white" style="grid-template-columns:minmax(0,1fr) 130px 75px 170px;"><div class="min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-[11px] font-bold tracking-wide text-gray-200 light:text-[#1a2e28]" title="<?php echo e($question->content); ?>"><?php echo e($question->content); ?></div><div class="flex w-[130px] shrink-0 justify-center"><span class="hidden rounded-full px-4 py-1 text-[10px] font-bold uppercase text-white sm:inline-flex <?php echo e($question->question_type==='essay'?'bg-purple-500':'bg-blue-500'); ?>"><?php echo e($question->question_type==='essay'?'ESAI':'PILIHAN GANDA'); ?></span></div><div class="flex w-[85px] shrink-0 justify-center"><span class="hidden min-w-[64px] justify-center rounded-full px-3 py-1 text-[10px] font-bold uppercase text-white sm:inline-flex <?php echo e($question->difficulty==='easy'?'bg-green-700':($question->difficulty==='medium'?'bg-orange-500':'bg-red-500')); ?>"><?php echo e($question->difficulty==='easy'?'Mudah':($question->difficulty==='medium'?'Sedang':'Sulit')); ?></span></div><div class="flex w-[170px] shrink-0 items-center justify-center gap-3"><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit bank soal')): ?><a href="<?php echo e(route('bank-soal.edit',$question)); ?>" class="inline-flex h-7 w-6 items-center justify-center border border-green-300 bg-[#DCFCE7] text-[#065f46] light:border-cyan-300 light:bg-[#DBFCFF] light:text-[#00363A]" aria-label="Edit soal"><svg class="h-5 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a><?php endif; ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete bank soal')): ?><button type="button" onclick="openDeleteBankModal(<?php echo e($question->id); ?>)" class="inline-flex h-7 w-6 items-center justify-center border border-red-300 bg-[#FFDBDB] text-[#3A0500]" aria-label="Hapus soal"><svg class="h-5 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4v3M4 7h16"/></svg></button><?php endif; ?></div></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="border border-[#3b494b] px-6 py-14 text-center text-gray-500">Belum ada soal.</div><?php endif; ?>
            </div>
        </section>
        <?php if($questionBanks->hasPages()): ?>
            <?php
                $pageUrl = fn ($p) => $questionBanks->url($p);
            ?>
            <div class="mt-5 flex items-center justify-between flex-wrap gap-2 border border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
                <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                    Total_Data: <?php echo e(number_format($questionBanks->total())); ?>

                    &nbsp;·&nbsp;
                    Menampilkan: <?php echo e(str_pad($questionBanks->firstItem() ?? 0, 2, '0', STR_PAD_LEFT)); ?>-<?php echo e(str_pad($questionBanks->lastItem() ?? 0, 2, '0', STR_PAD_LEFT)); ?>

                </span>
                <div class="flex items-center gap-1">
                    <a href="<?php echo e($questionBanks->onFirstPage() ? '#' : $pageUrl($questionBanks->currentPage() - 1)); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition <?php echo e($questionBanks->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>">&lsaquo;</a>
                    <?php
                        $start = max(1, min($questionBanks->currentPage() - 1, $questionBanks->lastPage() - 2));
                        $end = min($questionBanks->lastPage(), $start + 2);
                    ?>
                    <?php for($p = $start; $p <= $end; $p++): ?>
                        <a href="<?php echo e($pageUrl($p)); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition <?php echo e($p === $questionBanks->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>"><?php echo e(str_pad($p, 2, '0', STR_PAD_LEFT)); ?></a>
                    <?php endfor; ?>
                    <a href="<?php echo e($questionBanks->hasMorePages() ? $pageUrl($questionBanks->currentPage() + 1) : '#'); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition <?php echo e($questionBanks->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>">&rsaquo;</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete bank soal')): ?><div id="deleteBankModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16"><div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] light:bg-white"><div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:bg-[#003033]"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-white">HAPUS SOAL</span></div><div class="px-6 py-3"><p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Soal yang dihapus tidak dapat digunakan kembali.<br>Apakah Anda yakin ingin melanjutkan?</p><div class="flex justify-center py-0.5 text-[#ff3038]"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg></div><div class="flex justify-end gap-3 pt-3"><button type="button" onclick="closeDeleteBankModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe]">BATAL</button><button type="button" onclick="confirmDeleteBank()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7]">HAPUS</button></div></div></div></div><form id="deleteBankForm" method="POST" class="hidden"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form><?php endif; ?>
    <script>let deleteBankId=null;function openDeleteBankModal(id){deleteBankId=id;document.getElementById('deleteBankModal').classList.replace('hidden','flex')}function closeDeleteBankModal(){deleteBankId=null;document.getElementById('deleteBankModal').classList.replace('flex','hidden')}function confirmDeleteBank(){if(!deleteBankId)return;const f=document.getElementById('deleteBankForm');f.action='/bank-soal/'+deleteBankId;f.submit()}document.getElementById('deleteBankModal')?.addEventListener('click',e=>{if(e.target===e.currentTarget)closeDeleteBankModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDeleteBankModal()});</script>
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
<?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenchallenge/questionbank/index.blade.php ENDPATH**/ ?>