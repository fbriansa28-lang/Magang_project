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
     <?php $__env->slot('header', null, []); ?> <div class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#1a2e28]">EDIT MATERI — KELOLA BELAJAR</div> <?php $__env->endSlot(); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit kelola learning')): ?>
    <div class="mx-auto max-w-[1180px] pb-12"><?php if($errors->any()): ?><div class="mb-4 border border-red-500/25 bg-red-500/10 px-3.5 py-2.5 text-[12px] text-red-400 light:bg-red-50 light:text-red-700"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="block"><?php echo e($error); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?><form id="learning-form" method="POST" action="<?php echo e(route('learning-materials.update',$material)); ?>" enctype="multipart/form-data"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?> <?php echo $__env->make('managemenlearning.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></form>
    <?php if(isset($material->attachments)): ?>
    <?php $__currentLoopData = $material->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <form id="del-att-<?php echo e($att->id); ?>" action="<?php echo e(route('learning-materials.attachments.destroy', [$material->id, $att->id])); ?>" method="POST" class="hidden">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
    </form>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit kelola learning')): ?>
        <div id="deleteAttachmentModal" class="fixed inset-0 z-50 hidden items-start justify-center bg-black/65 px-4 pt-16"><div class="h-[213px] w-full max-w-[470px] overflow-hidden rounded-md border border-[#ff3038] bg-[#202020] shadow-[0_0_24px_rgba(255,48,56,0.2)] light:bg-white"><div class="flex h-8 items-center gap-2 border-b border-[#3b3d42] bg-[#292929] px-3 light:border-[#003033] light:bg-[#003033]"><span class="h-2.5 w-2.5 rounded-full bg-fuchsia-500"></span><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span><span class="ml-2 font-mono text-[11px] font-bold uppercase tracking-[0.12em] text-gray-200 light:text-white">HAPUS LAMPIRAN</span></div><div class="px-6 py-3"><p class="font-mono text-[13px] leading-5 text-gray-200 light:text-[#003033]">Lampiran yang dihapus tidak dapat dipulihkan.<br>Apakah Anda yakin ingin melanjutkan?</p><div class="flex justify-center py-0.5 text-[#ff3038]"><svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linejoin="round" stroke-width="1.8" d="M10.3 3.5 2.2 18a1.5 1.5 0 0 0 1.3 2.2h17a1.5 1.5 0 0 0 1.3-2.2L13.7 3.5a2 2 0 0 0-3.4 0Z"/><path stroke-linecap="round" stroke-width="2" d="M12 9v4m0 3h.01"/></svg></div><div class="flex justify-end gap-3 pt-3"><button type="button" onclick="closeDeleteAttachmentModal()" class="border border-[#4f8cff] bg-[#123b78] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#dbeafe] hover:bg-[#174b96]">BATAL</button><button type="button" onclick="confirmDeleteAttachment()" class="border border-[#ff5f57] bg-[#4b1115] px-3 py-1.5 font-mono text-[10px] font-bold uppercase text-[#ffd7d7] hover:bg-[#64171d]">HAPUS</button></div></div></div></div>
        <script>let deleteAttachmentId=null;function openDeleteAttachmentModal(id){deleteAttachmentId=id;document.getElementById('deleteAttachmentModal').classList.replace('hidden','flex')}function closeDeleteAttachmentModal(){deleteAttachmentId=null;document.getElementById('deleteAttachmentModal').classList.replace('flex','hidden')}function confirmDeleteAttachment(){if(!deleteAttachmentId)return;document.getElementById('del-att-'+deleteAttachmentId)?.submit()}document.getElementById('deleteAttachmentModal')?.addEventListener('click',e=>{if(e.target===e.currentTarget)closeDeleteAttachmentModal()});document.addEventListener('keydown',e=>{if(e.key==='Escape')closeDeleteAttachmentModal()});</script>
        <?php endif; ?>
    </div>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenlearning/edit.blade.php ENDPATH**/ ?>