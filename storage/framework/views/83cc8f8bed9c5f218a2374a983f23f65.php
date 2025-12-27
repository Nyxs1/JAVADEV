<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('pages.landingpage', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\JAVADEV\resources\views/index.blade.php ENDPATH**/ ?>