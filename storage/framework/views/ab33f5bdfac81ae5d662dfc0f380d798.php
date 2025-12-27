
<section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#2B7FFF]">

    
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute inset-0 opacity-60 hero-pattern pattern-container">
            <img src="<?php echo e(asset('assets/patterns/Pattern.png')); ?>" alt="Pattern"
                class="w-full h-full object-cover animate-pattern-sway gpu-accelerated" loading="lazy">
        </div>

        
        <?php for($row = 1; $row <= 6; $row++): ?>
            <?php
                $positions = [
                    1 => [['right' => '20%', 'tops' => ['10%', '25%', '40%', '55%', '70%', '85%']]],
                    2 => [['right' => '14%', 'tops' => ['15%', '30%', '45%', '60%', '75%', '90%']]],
                    3 => [['right' => '8%', 'tops' => ['12%', '27%', '42%', '57%', '72%', '87%']]],
                    4 => [['right' => '2%', 'tops' => ['18%', '33%', '48%', '63%', '78%', '93%']]],
                    5 => [['right' => '26%', 'tops' => ['5%', '22%', '38%', '52%', '65%', '82%']]],
                    6 => [['right' => '32%', 'tops' => ['8%', '28%', '45%', '68%', '85%']]],
                ];
            ?>
            <?php $__currentLoopData = $positions[$row][0]['tops']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $top): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="plus-sign" style="right: <?php echo e($positions[$row][0]['right']); ?>; top: <?php echo e($top); ?>;"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endfor; ?>

        
        <?php $__currentLoopData = [
            ['38%', '18%'], ['38%', '35%'], ['38%', '58%'], ['38%', '78%'],
            ['35%', '12%'], ['45%', '6%'], ['50%', '15%'],
            ['55%', '88%'], ['35%', '95%'], ['48%', '92%']
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$right, $top]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="plus-sign" style="right: <?php echo e($right); ?>; top: <?php echo e($top); ?>;"></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="absolute top-20 -left-20 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-glow-pulse gpu-accelerated"></div>
        <div class="absolute bottom-40 -right-20 w-80 h-80 bg-white/5 rounded-full blur-3xl animate-glow-pulse-delay gpu-accelerated"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-white/3 rounded-full blur-3xl animate-glow-slow gpu-accelerated"></div>
    </div>

    
    <div class="relative z-10 w-full py-20">
        <div class="lp-container">

            
            <div class="text-center mb-12 animate-fade-in">
                <div class="inline-flex items-center gap-3 bg-white/20 backdrop-blur-sm px-8 py-4 rounded-2xl shadow-lg border border-white/30">
                    <img src="<?php echo e(asset('assets/icons/terminal-icon.svg')); ?>" alt="Terminal Icon" class="w-8 h-8">
                    <span class="text-white font-semibold text-lg">Build, Learn, Collaborate, Repeat</span>
                </div>
            </div>

            
            <div class="relative flex items-center justify-center mb-20">

                
                <div class="absolute -left-24 xl:-left-36 top-[58%] transform -translate-y-1/2 hidden lg:block animate-fade-in-left pointer-events-none">
                    <img src="<?php echo e(asset('assets/images/stickers/Memoji Boys 4-16 1.png')); ?>" alt="Developer"
                        class="w-44 xl:w-56 h-auto drop-shadow-2xl gpu-accelerated" loading="lazy">
                </div>

                
                <div class="text-center max-w-4xl mx-auto px-4 animate-fade-in-delay hero-content">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 leading-tight">
                        A Continuous Journey to<br>Create, Learn, and Evolve.
                    </h1>
                    <p class="text-base sm:text-lg lg:text-xl text-white/90 max-w-2xl mx-auto leading-relaxed mb-10">
                        Kami percaya setiap developer hebat lahir dari siklus tak berujung:<br>
                        membangun, belajar, dan berkolaborasi. JAVADEV hadir untuk<br>
                        membentuk generasi pembuat solusi yang siap menghadapi<br>
                        tantangan dunia digital.
                    </p>

                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <button class="w-48 py-3 bg-white text-blue-600 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            Lihat Event
                        </button>
                        <button class="w-48 py-3 border-2 border-white text-white text-lg font-semibold rounded-xl hover:bg-white hover:text-blue-600 transition-all duration-300">
                            Kelas
                        </button>
                    </div>
                </div>

                
                <div class="absolute -right-24 xl:-right-36 top-[58%] transform -translate-y-1/2 hidden lg:block animate-fade-in-right pointer-events-none">
                    <img src="<?php echo e(asset('assets/images/stickers/STICKER.png')); ?>" alt="Sticker"
                        class="w-44 xl:w-56 h-auto drop-shadow-2xl gpu-accelerated" loading="lazy">
                </div>
            </div>

            
            <div class="text-center">
                <button id="scroll-down-btn" onclick="scrollToNextSection()"
                    class="inline-flex flex-col items-center group cursor-pointer hover:scale-110 transition-transform duration-300">
                    <div class="relative">
                        <svg class="w-28 h-28 animate-spin-slow" viewBox="0 0 140 140">
                            <defs>
                                <path id="scrollCircle" d="M 70,70 m -50,0 a 50,50 0 1,1 100,0 a 50,50 0 1,1 -100,0" />
                            </defs>
                            <text class="text-[16px] fill-white font-medium">
                                <textPath href="#scrollCircle" startOffset="0%">
                                    Scroll Ke Bawah
                                </textPath>
                            </text>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img src="<?php echo e(asset('assets/icons/arrow-down-icon.svg')); ?>" alt="Scroll Down" class="w-5 h-5 text-white">
                        </div>
                    </div>
                </button>
            </div>

        </div>
    </div>
</section>
<?php /**PATH C:\laragon\www\JAVADEV\resources\views/components/landing/hero-section.blade.php ENDPATH**/ ?>