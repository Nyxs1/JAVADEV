<header id="navbar"
    class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-b border-slate-200 transition-all">
    <div class="max-w-7xl mx-auto h-16 px-4 sm:px-8 flex items-center justify-between">

        
        <a href="<?php echo e(route('index')); ?>" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <img src="<?php echo e(asset('assets/images/logos/Logo-Photoroom.png')); ?>" alt="JavaDev Logo" class="h-12 w-auto">
        </a>

        
        <nav class="hidden md:flex items-center gap-10 text-sm font-medium text-slate-600">
            <a href="<?php echo e(route('index')); ?>#courses" class="nav-link hover:text-slate-900">Course</a>
            <a href="<?php echo e(route('events.index')); ?>" class="nav-link hover:text-slate-900">Event</a>
            <a href="<?php echo e(route('index')); ?>#portfolio" class="nav-link hover:text-slate-900">Portfolio</a>
            <a href="<?php echo e(route('index')); ?>#leaderboard" class="nav-link hover:text-slate-900">Leaderboard</a>
        </nav>

        
        <?php if(auth()->guard()->check()): ?>
            
            <div class="relative" id="avatar-dropdown">
                <button type="button"
                    class="avatar-trigger flex items-center gap-2 p-1 rounded-full hover:bg-slate-100 transition-colors"
                    id="avatar-button" aria-expanded="false" aria-haspopup="true">

                    
                    <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['user' => auth()->user(),'size' => 'md','shape' => 'circle','id' => 'avatar-circle','class' => 'ring-2 ring-transparent transition-all']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()),'size' => 'md','shape' => 'circle','id' => 'avatar-circle','class' => 'ring-2 ring-transparent transition-all']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                </button>

                
                <div class="avatar-dropdown-menu" id="avatar-menu" role="menu">
                    <div class="avatar-dropdown-content">
                        <a href="<?php echo e(route('users.dashboard', auth()->user()->username)); ?>"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
                            role="menuitem">
                            <img src="<?php echo e(asset('assets/icons/home.svg')); ?>" alt="" class="w-4 h-4 opacity-70">
                            Dashboard
                        </a>

                        <a href="<?php echo e(route('profile.show', auth()->user()->username)); ?>"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
                            role="menuitem">
                            <img src="<?php echo e(asset('assets/icons/user.svg')); ?>" alt="" class="w-4 h-4 opacity-70">
                            Profile
                        </a>

                        <a href="<?php echo e(route('profile.settings')); ?>"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors"
                            role="menuitem">
                            <img src="<?php echo e(asset('assets/icons/settings.svg')); ?>" alt="" class="w-4 h-4 opacity-70">
                            Settings
                        </a>

                        <hr class="my-1 border-slate-100">

                        <form method="POST" action="<?php echo e(route('logout')); ?>" class="block">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"
                                role="menuitem">
                                <img src="<?php echo e(asset('assets/icons/logout.svg')); ?>" alt="" class="w-4 h-4 opacity-70">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('login')); ?>"
                    class="px-5 py-2 rounded-xl bg-[#246CF9] text-white text-sm font-semibold shadow-md hover:bg-[#1c55c9] transition-colors">
                    Login
                </a>
                <a href="<?php echo e(route('register')); ?>"
                    class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                    Register
                </a>
            </div>
        <?php endif; ?>
    </div>
</header><?php /**PATH C:\laragon\www\JAVADEV\resources\views/partials/layout/navbar.blade.php ENDPATH**/ ?>