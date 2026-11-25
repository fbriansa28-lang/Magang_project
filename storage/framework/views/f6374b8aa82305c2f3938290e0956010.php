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
        <h2 class="font-semibold text-xl text-slate-200 light:text-gray-800 leading-tight">
            <?php echo e(__('PROFIL')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <?php
        $user = Auth::user();

        $fieldClass = 'w-full box-border bg-[#0f1017] light:bg-[#f3f7f5] border border-[#262a3d] light:border-[#dbe6e2] px-3 py-2.5 text-[13px] font-mono text-slate-100 light:text-[#1a2e28] outline-none pointer-events-none select-none cursor-default';

        $fields = [
            ['label' => 'Username', 'type' => 'text',     'value' => $user->username],
            ['label' => 'Nama',     'type' => 'text',     'value' => $user->name],
            ['label' => 'Email',    'type' => 'email',    'value' => $user->email],
            ['label' => 'Password', 'type' => 'password', 'value' => '**************'],
        ];
    ?>

    <div class="flex justify-center w-full">

        <div class="w-full max-w-lg bg-[#161822] light:bg-white border border-[#1e2130] light:border-[#e5e7eb] p-6">

            
            <div class="flex justify-center mb-5">
                <div class="w-[140px] h-[140px] overflow-hidden border border-[#30A192]/60 bg-[#0f1017] light:bg-[#f3f7f5] flex items-center justify-center">
                    <?php if($user->image): ?>
                        <img src="<?php echo e($user->image_url); ?>"
                             alt="<?php echo e($user->name); ?>"
                             class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-4xl font-bold text-[#30A192] font-mono">
                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="flex justify-center mb-8">
                <a href="<?php echo e(route('profile.edit')); ?>"
                   class="inline-block text-center w-36 py-2 text-[#0d0e12] text-[11px] font-bold tracking-widest bg-[#30A192] hover:opacity-90 transition">
                    EDIT PROFIL
                </a>
            </div>

            
            <div class="flex flex-col gap-4">
                <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label class="block mb-1.5 text-[11px] font-normal text-gray-400 light:text-[#6b7568] uppercase font-sans">
                            <?php echo e($field['label']); ?>

                        </label>
                        <input type="<?php echo e($field['type']); ?>"
                               value="<?php echo e($field['value']); ?>"
                               readonly
                               tabindex="-1"
                               aria-readonly="true"
                               class="<?php echo e($fieldClass); ?>">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
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
<?php endif; ?><?php /**PATH D:\laragon\www\ctf_project\resources\views/profile/index.blade.php ENDPATH**/ ?>