<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['courses' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['courses' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $defaultCourses = [
        ['icon' => 'project-manager-icon.svg', 'title' => 'Project Manager', 'desc' => 'Kelola proyek dengan efektif dan efisien'],
        ['icon' => 'vibe-code-icon.svg', 'title' => 'Vibe Code', 'desc' => 'Coding dengan vibe yang menyenangkan'],
        ['icon' => 'uiux-design-icon.svg', 'title' => 'UI/UX Design', 'desc' => 'Desain interface yang user-friendly'],
        ['icon' => 'mobile-code-icon.svg', 'title' => 'Mobile Code', 'desc' => 'Kembangkan aplikasi mobile native'],
    ];
    $coursesData = $courses ?? $defaultCourses;
?>


<section id="courses" class="courses-section">
    
    <div class="courses-pattern" aria-hidden="true">
        <img src="<?php echo e(asset('assets/patterns/Pattern-Bot.png')); ?>" alt="" class="courses-pattern-img">
    </div>

    <div class="courses-inner">
        <div class="courses-head">
            <h2>Pilih Course</h2>
            <p>Jelajahi kelas yang tersedia dan pilihlah yang sejalan dengan tujuanmu</p>
        </div>

        <div class="courses-grid">
            <?php $__currentLoopData = $coursesData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="course-card">
                    <div class="course-icon">
                        <img src="<?php echo e(asset('assets/icons/' . $course['icon'])); ?>" alt="<?php echo e($course['title']); ?>">
                    </div>
                    <h3><?php echo e($course['title']); ?></h3>
                    <p><?php echo e($course['desc']); ?></p>
                    <a href="#" class="course-btn">Lebih Lanjut</a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="courses-more">
            <a href="#"
                class="text-blue-600 font-semibold text-lg hover:underline transition-all duration-300 cursor-pointer">
                Lihat Lebih Banyak
            </a>
        </div>
    </div>
</section><?php /**PATH C:\laragon\www\JAVADEV\resources\views/components/landing/courses-section.blade.php ENDPATH**/ ?>