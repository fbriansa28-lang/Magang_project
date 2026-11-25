
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-screen overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    
    <title><?php echo e(config('app.name', 'PETROCYBER CHALLENGE')); ?><?php echo e(isset($header) ? ' | ' . strip_tags($header) : ''); ?></title>

    
    <link rel="icon" type="image/png" href="<?php echo e(asset('image/logo-PG-agro-smalll.png')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('image/logo-PG-agro-smalll.png')); ?>">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <script>
        (function () {
            var theme = localStorage.getItem('theme') || 'dark';
            if (theme === 'light') {
                document.documentElement.classList.add('light');
            }
        })();
    </script>

    <style>[x-cloak] { display: none !important; }</style>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-['Figtree',sans-serif] bg-[#0d0e12] light:bg-[#ECECEC] antialiased transition-colors h-screen overflow-hidden">

    
    <div class="grid grid-cols-[240px_minmax(0,1fr)] grid-rows-[1fr] h-screen overflow-hidden bg-[#0d0e12] light:bg-[#ECECEC] transition-colors">

        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex flex-col min-h-0 overflow-hidden">

            <header class="shrink-0 bg-[#161822] light:bg-[#F4F4F5] border-b border-[#1e2130] light:border-[#003033] px-6 h-14 flex items-center justify-between transition-colors">
                <div class="text-[17px] font-extrabold text-slate-50 light:text-[#1a2e28] leading-[1.2] tracking-tight text-sm uppercase">
                    <?php echo e($header ?? ''); ?>

                </div>

                <?php echo $__env->make('layouts.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </header>

            <main class="flex-1 overflow-y-auto min-h-0 p-4 xl:p-6 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <?php if (isset($component)) { $__componentOriginalbb0843bd48625210e6e530f88101357e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb0843bd48625210e6e530f88101357e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.flash-message','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('flash-message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb0843bd48625210e6e530f88101357e)): ?>
<?php $attributes = $__attributesOriginalbb0843bd48625210e6e530f88101357e; ?>
<?php unset($__attributesOriginalbb0843bd48625210e6e530f88101357e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb0843bd48625210e6e530f88101357e)): ?>
<?php $component = $__componentOriginalbb0843bd48625210e6e530f88101357e; ?>
<?php unset($__componentOriginalbb0843bd48625210e6e530f88101357e); ?>
<?php endif; ?>
                <?php echo e($slot); ?>

            </main>

        </div>
    </div>
</body>
</html>
<?php /**PATH D:\laragon\www\ctf_project\resources\views/layouts/app.blade.php ENDPATH**/ ?>