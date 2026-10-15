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
     <?php $__env->slot('header', null, []); ?> <div class="text-[15px] font-semibold tracking-wide text-gray-200 light:text-[#1a2e28]">EDIT SOAL — BANK SOAL</div> <?php $__env->endSlot(); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit bank soal')): ?>
    <div class="mx-auto max-w-[820px] pb-12"><?php if($errors->any()): ?><div class="mb-4 flex flex-col gap-1 border border-red-500/25 bg-red-500/10 px-3.5 py-2.5 text-[12px] text-red-400 light:bg-red-50 light:text-red-700"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($error); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?> <?php if($usageCount > 0): ?><div class="mb-4 border border-cyan-500/25 bg-cyan-500/10 px-3.5 py-2.5 text-[12px] text-cyan-300 light:border-cyan-700/40 light:bg-cyan-50 light:text-[#006b73]">Soal ini sudah pernah muncul di <?php echo e($usageCount); ?> attempt peserta.</div><?php endif; ?><div class="border border-[#1f242c] border-t-2 border-t-fuchsia-500 bg-[#12161c] p-6 light:bg-white"><form id="bank-soal-form" method="POST" action="<?php echo e(route('bank-soal.update',$question)); ?>" enctype="multipart/form-data"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?> <?php echo $__env->make('managemenchallenge.questionbank.partials.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><div class="mt-6 flex justify-end gap-3 pt-6"><a href="<?php echo e(route('bank-soal.index')); ?>" class="border border-[#ff5f57] bg-[#4b1115] px-5 py-2 text-[10px] font-bold uppercase text-[#ffd7d7]">BATAL</a><button type="submit" class="inline-flex items-center gap-1.5 border border-[#4f8cff] bg-[#123b78] px-5 py-2 text-[10px] font-bold uppercase text-[#dbeafe]"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 4h11l3 3v13H5V4Zm3 0v5h7V4M8 15h8v5H8v-5Z"/></svg>SIMPAN</button></div></form></div></div></div>
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
<?php endif; ?>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/managemenchallenge/questionbank/edit.blade.php ENDPATH**/ ?>