<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['events' => null]));

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

foreach (array_filter((['events' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $defaultEvents = [
        ['image' => 'Who We Are.png', 'title' => 'Kelas UI/UX Design', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'profesional.png', 'title' => 'Vibe Code With Framer', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'student.png', 'title' => 'Build Website From Scratch...', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'Who We Are.png', 'title' => 'Build Apps With Kotlin', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'profesional.png', 'title' => 'How To Start Data Analys', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
        ['image' => 'student.png', 'title' => 'Ethical Hacker : Penetration...', 'date' => '12-15 Desember 2025', 'location' => 'Ruang Lakoda'],
    ];
    $eventsData = $events ?? $defaultEvents;
?>


<section id="events" class="py-20 bg-white">
    <div class="lp-container">

        
        <div class="text-center mb-16 events-header">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6 animate-on-scroll events-title">
                Agenda Acara/Kegiatan
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto animate-on-scroll events-desc">
                Ikuti berbagai event menarik yang kami selenggarakan untuk mengembangkan skill dan networking
            </p>
        </div>

        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12 events-cards-grid">
            <?php $__currentLoopData = $eventsData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div
                    class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 animate-on-scroll event-card">
                    <div class="h-48 overflow-hidden">
                        <img src="<?php echo e(asset('assets/images/photos/' . $event['image'])); ?>" alt="<?php echo e($event['title']); ?>"
                            class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-3"><?php echo e($event['title']); ?></h3>

                        
                        <div class="flex items-center gap-4 mb-4 text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                <img src="<?php echo e(asset('assets/icons/calendar-icon.svg')); ?>" alt="Calendar" class="w-4 h-4">
                                <span><?php echo e($event['date']); ?></span>
                            </div>
                            <div class="flex items-center gap-1">
                                <img src="<?php echo e(asset('assets/icons/location-icon.svg')); ?>" alt="Location" class="w-4 h-4">
                                <span><?php echo e($event['location']); ?></span>
                            </div>
                        </div>

                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    <?php for($i = 1; $i <= 3; $i++): ?>
                                        <div
                                            class="w-8 h-8 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center">
                                            <span class="text-xs text-gray-500 font-medium">U<?php echo e($i); ?></span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-sm text-gray-600">Members Join</span>
                            </div>
                            <button
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                                Join Event
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="text-center animate-on-scroll">
            <a href="#"
                class="text-blue-600 font-semibold text-lg hover:underline transition-all duration-300 cursor-pointer">
                Lihat Semua Events
            </a>
        </div>

    </div>
</section><?php /**PATH C:\laragon\www\JAVADEV\resources\views/components/landing/events-section.blade.php ENDPATH**/ ?>