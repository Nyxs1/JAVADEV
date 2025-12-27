<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>JavaDev - <?php echo $__env->yieldContent('title', 'Home'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/images/logos/logo-for-tab.png')); ?>">

    
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="bg-[#F5F7FB] text-slate-900 antialiased">

    
    <?php if(session('success') || session('error') || session('info')): ?>
        <div id="flash-data" class="hidden" data-success="<?php echo e(session('success')); ?>" data-error="<?php echo e(session('error')); ?>"
            data-info="<?php echo e(session('info')); ?>">
        </div>
    <?php endif; ?>

    
    <?php echo $__env->make('partials.layout.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <?php if(session('logout_success')): ?>
        <div id="logout-notification"
            class="fixed bottom-5 right-5 bg-slate-800 text-white px-4 py-2.5 rounded-lg shadow-lg z-50 flex items-center gap-2"
            style="transform: translateX(100%);">
            <svg class="w-4 h-4 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium"><?php echo e(session('logout_success')); ?></span>
        </div>
        <script>
            (function () {
                const el = document.getElementById('logout-notification');
                if (!el) return;
                // Slide in
                requestAnimationFrame(() => {
                    el.style.transition = 'transform 0.3s ease-out';
                    el.style.transform = 'translateX(0)';
                });
                // Auto-dismiss after 2s
                setTimeout(() => {
                    el.style.transition = 'transform 0.3s ease-in, opacity 0.3s ease-in';
                    el.style.transform = 'translateX(100%)';
                    el.style.opacity = '0';
                    setTimeout(() => el.remove(), 300);
                }, 2000);
            })();
        </script>
    <?php endif; ?>

    
    <div class="h-16"></div>

    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <?php echo $__env->make('partials.layout.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>

</html><?php /**PATH C:\laragon\www\JAVADEV\resources\views/layouts/app.blade.php ENDPATH**/ ?>