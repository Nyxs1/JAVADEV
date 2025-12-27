<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['achievements' => null]));

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

foreach (array_filter((['achievements' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $defaultAchievements = [
        ['image' => 'Juara.png', 'title' => 'Juara 1 Hackathon 2024', 'desc' => 'Tim JAVADEV berhasil meraih juara pertama dalam kompetisi hackathon tingkat nasional'],
        ['image' => 'Juara.png', 'title' => 'Best Innovation Award', 'desc' => 'Penghargaan inovasi terbaik untuk solusi digital yang berdampak sosial'],
        ['image' => 'Juara.png', 'title' => 'Tech Conference Speaker', 'desc' => 'Anggota komunitas menjadi pembicara di konferensi teknologi internasional'],
        ['image' => 'Juara.png', 'title' => 'Startup Competition Winner', 'desc' => 'Memenangkan kompetisi startup dengan ide bisnis berbasis teknologi'],
        ['image' => 'Juara.png', 'title' => 'Community Impact Award', 'desc' => 'Penghargaan atas kontribusi positif terhadap pengembangan komunitas tech'],
        ['image' => 'Juara.png', 'title' => 'Open Source Contributor', 'desc' => 'Kontribusi aktif dalam proyek open source yang digunakan secara global'],
    ];
    $achievementsData = $achievements ?? $defaultAchievements;
?>


<section id="achievement" class="py-24 bg-white overflow-hidden">
    
    <div class="lp-container">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4 animate-on-scroll">Achievement</h2>
            <p class="text-gray-600 text-lg animate-on-scroll">
                Pencapaian dan prestasi yang telah diraih bersama komunitas
            </p>
        </div>
    </div>

    
    <div class="achievement-gallery-container">
        <div class="achievement-gallery">
            <?php $__currentLoopData = $achievementsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="achievement-item">
                    <div class="achievement-image">
                        <img src="<?php echo e(asset('assets/images/photos/' . $achievement['image'])); ?>"
                            alt="<?php echo e($achievement['title']); ?>">
                    </div>
                    <div class="achievement-content">
                        <h3 class="achievement-title"><?php echo e($achievement['title']); ?></h3>
                        <p class="achievement-desc"><?php echo e($achievement['desc']); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php $__currentLoopData = array_slice($achievementsData, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $achievement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="achievement-item">
                    <div class="achievement-image">
                        <img src="<?php echo e(asset('assets/images/photos/' . $achievement['image'])); ?>"
                            alt="<?php echo e($achievement['title']); ?>">
                    </div>
                    <div class="achievement-content">
                        <h3 class="achievement-title"><?php echo e($achievement['title']); ?></h3>
                        <p class="achievement-desc"><?php echo e($achievement['desc']); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section><?php /**PATH C:\laragon\www\JAVADEV\resources\views/components/landing/achievement-section.blade.php ENDPATH**/ ?>