<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tools' => null]));

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

foreach (array_filter((['tools' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $defaultTools = [
        ['logo' => 'logos_css-3.png', 'name' => 'CSS'],
        ['logo' => 'logos_bootstrap.png', 'name' => 'Bootstrap'],
        ['logo' => 'logos_nextjs.png', 'name' => 'Next Js'],
        ['logo' => 'logos_php.png', 'name' => 'PHP'],
        ['logo' => 'logos_go.png', 'name' => 'Golang'],
        ['logo' => 'logos_javascript.png', 'name' => 'Javascript'],
        ['logo' => 'logos_figma.png', 'name' => 'Figma'],
        ['logo' => 'logos_nodejs.png', 'name' => 'Node Js'],
        ['logo' => 'logos_adobe-illustrator.png', 'name' => 'Adobe Illustrator'],
        ['logo' => 'logos_adobe-photoshop.png', 'name' => 'Adobe Photoshop'],
        ['logo' => 'logos_laravel.png', 'name' => 'Laravel'],
        ['logo' => 'logos_java.png', 'name' => 'Java'],
    ];
    $toolsData = $tools ?? $defaultTools;
?>


<section id="tools" class="tools-section">
    <div class="lp-container">
        <div class="tools-wrap">

            
            <div class="tools-header">
                <h2 class="tools-title animate-on-scroll">
                    Tools & Apps Yang Kita Pelajari
                </h2>
                <p class="tools-desc animate-on-scroll">
                    Kami belajar berbagai tools dan aplikasi yang menjadi bagian dari keseharian developer dan kreator
                    masa kini.<br>
                    Dari desain, pengembangan, hingga kolaborasi semua kami pelajari bersama sebagai komunitas yang
                    ingin tumbuh<br>
                    dan beradaptasi dengan dunia digital yang terus berubah.
                </p>
            </div>

            
            <div class="tools-grid">
                <?php $__currentLoopData = $toolsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="tool-item">
                        <img src="<?php echo e(asset('assets/tech-logos/' . $tool['logo'])); ?>" alt="<?php echo e($tool['name']); ?>">
                        <span><?php echo e($tool['name']); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        </div>
    </div>
</section><?php /**PATH C:\laragon\www\JAVADEV\resources\views/components/landing/tools-section.blade.php ENDPATH**/ ?>