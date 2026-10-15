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
     <?php $__env->slot('header', null, []); ?> <h2 class="text-[17px] font-extrabold tracking-tight text-slate-50 light:text-[#003033]">KELOLA KATEGORI</h2> <?php $__env->endSlot(); ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view kategori')): ?>
    <div class="mt-5 w-full max-w-[960px] font-mono text-[10px]">
        <div class="items-start" style="display:grid;grid-template-columns:220px minmax(0,1fr);gap:18px;">
            <div class="min-w-0 space-y-4" style="grid-column:1;">
                <section class="h-[105px] border-l-4 border-violet-500 bg-[#1b1d1f] px-4 py-3 light:bg-white">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-gray-300 light:text-gray-600">Total Kategori</p>
                    <p class="mt-1 text-[38px] leading-none text-violet-400 light:text-violet-600"><?php echo e($categories->total()); ?></p>
                    <p class="mt-3 text-[10px] font-bold uppercase tracking-wide text-lime-400 light:text-violet-600">+<?php echo e($categories->total()); ?> Baru</p>
                </section>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create kategori')): ?><a href="<?php echo e(route('kategori.create')); ?>" class="flex h-9 items-center justify-center gap-3 bg-[#30A192] px-5 text-[10px] font-bold uppercase text-[#061015] shadow-lg shadow-[#30A192]/20 hover:opacity-90 light:bg-[#2563eb] light:text-white"><span class="text-xl font-light">+</span> Tambah Kategori Baru</a><?php endif; ?>
            </div>

            <div class="min-w-0" style="grid-column:2;">
                <section class="overflow-hidden border border-[#263239] bg-[#0d0e12] light:border-[#3b494b] light:bg-[#d1d1d1]">
                    <div class="flex h-[44px] items-center justify-between gap-3 bg-[#25272a] px-4 text-[11px] font-bold uppercase tracking-[0.14em] text-white light:bg-[#003033]">
                        <div class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-1">DAFTAR_KATEGORI</span></div>
                        <div class="flex h-[28px] w-full max-w-[250px] items-center gap-3 border border-[#3b494b] bg-[#0f1017] px-3 light:bg-white"><svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/></svg><input type="search" placeholder="CARI..." autocomplete="off" oninput="filterCategories(this.value)" class="min-w-0 flex-1 border-none bg-transparent font-mono text-[10px] uppercase tracking-wide text-gray-200 outline-none placeholder:text-gray-500 focus:border-[#30A192] focus:ring-0 light:text-[#1a2e28]"></div>
                    </div>
                    <div class="overflow-x-auto"><table class="w-full min-w-[480px] border-collapse"><thead><tr class="h-[44px] border-b border-[#384147] bg-[#1b1d1f] light:bg-[#ECECEC]"><th class="w-[42%] px-6 py-3 text-left text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Kategori</th><th class="w-[36%] px-6 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Tantangan</th><th class="px-6 py-3 text-center text-[10px] font-bold uppercase tracking-wide text-gray-300 light:text-[#1a2e28]">Aksi</th></tr></thead><tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr class="category-row border-b border-[#20272a] bg-[#0d0e12] hover:bg-white/[0.03] light:border-[#b8b8b8] light:bg-white light:hover:bg-[#f5f5f5]" data-name="<?php echo e(strtolower($category->name)); ?>"><td class="px-4 py-2 text-left"><span class="inline-flex border border-gray-300 px-2 py-1 text-[11px] uppercase text-gray-200 light:border-gray-700 light:text-[#1a2e28]"><?php echo e($category->name); ?></span></td><td class="px-4 py-2 text-center text-[13px] text-gray-200 light:text-[#003033]"><?php echo e($category->challenges_count); ?></td><td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-3"><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit kategori')): ?><a href="<?php echo e(route('kategori.edit', $category)); ?>" class="w-6 h-7 inline-flex items-center justify-center gap-1 border border-green-300 bg-[#DCFCE7] text-[#065f46] light:border-cyan-300 light:bg-[#DBFCFF] light:text-[#00363A] text-[11px] font-semibold uppercase tracking-wide hover:bg-green-100 light:hover:bg-cyan-100 transition" aria-label="Edit kategori"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M11 5H6a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-5m-1.414-9.414a2 2 0 1 1 2.828 2.828L11.828 15H9v-2.828z"/></svg></a><?php endif; ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete kategori')): ?><button type="button" onclick="openDeleteCategoryModal(<?php echo e($category->id); ?>)" class="w-6 h-7 inline-flex items-center justify-center gap-1 border border-red-300 bg-[#FFDBDB] text-[#3A0500] text-[11px] font-semibold uppercase tracking-wide hover:bg-red-100 transition" aria-label="Hapus kategori"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16"/></svg></button><?php endif; ?></div></td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="3" class="py-14 text-center text-gray-500">Belum ada kategori.</td></tr><?php endif; ?>
                    </tbody></table></div>
                </section>
                <?php if($categories->hasPages()): ?>
                    <?php
                        $pageUrl = fn ($p) => $categories->url($p);
                    ?>
                    <div class="mt-5 flex items-center justify-between flex-wrap gap-2 border border-[#353534] bg-[#353534] px-4 py-3 light:border-[#e5e7eb] light:bg-[#9CA3AF]">
                        <span class="text-[10.5px] uppercase tracking-wide text-white light:text-black">
                            Total_Data: <?php echo e(number_format($categories->total())); ?>

                            &nbsp;·&nbsp;
                            Menampilkan: <?php echo e(str_pad($categories->firstItem() ?? 0, 2, '0', STR_PAD_LEFT)); ?>-<?php echo e(str_pad($categories->lastItem() ?? 0, 2, '0', STR_PAD_LEFT)); ?>

                        </span>
                        <div class="flex items-center gap-1">
                            <a href="<?php echo e($categories->onFirstPage() ? '#' : $pageUrl($categories->currentPage() - 1)); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition <?php echo e($categories->onFirstPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>">&lsaquo;</a>
                            <?php
                                $start = max(1, min($categories->currentPage() - 1, $categories->lastPage() - 2));
                                $end = min($categories->lastPage(), $start + 2);
                            ?>
                            <?php for($p = $start; $p <= $end; $p++): ?>
                                <a href="<?php echo e($pageUrl($p)); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] font-bold border transition <?php echo e($p === $categories->currentPage() ? 'bg-[#5c5c59] light:bg-[#003033] border-white light:border-[#003033] text-white' : 'bg-[#353534] light:bg-[#9CA3AF] border-white light:border-[#0d0e12] text-white light:text-[#0d0e12] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>"><?php echo e(str_pad($p, 2, '0', STR_PAD_LEFT)); ?></a>
                            <?php endfor; ?>
                            <a href="<?php echo e($categories->hasMorePages() ? $pageUrl($categories->currentPage() + 1) : '#'); ?>" class="w-7 h-7 flex items-center justify-center text-[11px] bg-[#353534] light:bg-[#9CA3AF] border border-white light:border-[#272b3a] transition <?php echo e($categories->onLastPage() ? 'text-white light:text-black pointer-events-none' : 'text-white light:text-[#1a2e28] hover:bg-[#0d0e12] light:hover:bg-black/5'); ?>">&rsaquo;</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete kategori')): ?><div id="deleteCategoryModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16"><div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] shadow-[0_0_24px_rgba(255,48,56,0.2)] light:bg-white"><div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:border-[#003033] light:bg-[#003033]"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-gray-200 light:text-white">HAPUS KATEGORI</span></div><div class="px-6 py-3"><p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Kategori yang dihapus tidak dapat digunakan kembali.<br>Apakah Anda yakin ingin melanjutkan?</p><div class="flex justify-center py-0.5 text-[#ff3038]"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg></div><div class="flex justify-end gap-3 pt-3"><button type="button" onclick="closeDeleteCategoryModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]">BATAL</button><button type="button" onclick="confirmDeleteCategory()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">HAPUS</button></div></div></div></div><form id="deleteCategoryForm" method="POST" class="hidden"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form><?php endif; ?>
    <script>let deleteCategoryId=null;function filterCategories(value){const q=value.toLowerCase().trim();document.querySelectorAll('.category-row').forEach(r=>r.classList.toggle('hidden',!r.dataset.name.includes(q)));}function openDeleteCategoryModal(id){deleteCategoryId=id;document.getElementById('deleteCategoryModal').classList.replace('hidden','flex');}function closeDeleteCategoryModal(){deleteCategoryId=null;document.getElementById('deleteCategoryModal').classList.replace('flex','hidden');}function confirmDeleteCategory(){if(!deleteCategoryId)return;const f=document.getElementById('deleteCategoryForm');f.action='/kategori/'+deleteCategoryId;f.submit();}document.getElementById('deleteCategoryModal')?.addEventListener('click',function(event){if(event.target===this)closeDeleteCategoryModal();});document.addEventListener('keydown',function(event){if(event.key==='Escape')closeDeleteCategoryModal();});</script>
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
<?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenchallenge/kategori/index.blade.php ENDPATH**/ ?>