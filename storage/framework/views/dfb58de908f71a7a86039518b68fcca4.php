<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'user',
    'size' => 'md',
    'shape' => 'circle',
    'class' => '',
]));

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

foreach (array_filter(([
    'user',
    'size' => 'md',
    'shape' => 'circle',
    'class' => '',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Size classes mapping
    $sizeClasses = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-16 h-16 text-xl',
        'xl' => 'w-24 h-24 text-3xl',
        '2xl' => 'w-32 h-32 text-4xl',
        'banner' => 'w-full aspect-video max-h-72',
    ];
    
    // Shape classes mapping
    $shapeClasses = [
        'circle' => 'rounded-full',
        'rounded' => 'rounded-xl',
        'rounded-lg' => 'rounded-2xl',
        'square' => 'rounded-lg',
        'banner' => 'rounded-none',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $shapeClass = $shapeClasses[$shape] ?? $shapeClasses['circle'];
    
    // Check if avatar file exists
    $hasAvatar = $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar);
    
    // Get initials for fallback
    $displayName = $user->username ?? $user->name ?? 'U';
    $initial = strtoupper(substr($displayName, 0, 1));
    
    // Avatar URL with cache busting
    $avatarUrl = $hasAvatar ? $user->avatar_url : null;
    
    // Focal point CSS (from User model)
    $focusStyle = $user->avatar_style ?? '';
?>

<div 
    <?php echo e($attributes->merge([
        'class' => "avatar-container {$sizeClass} {$shapeClass} {$class} bg-slate-200 flex items-center justify-center overflow-hidden relative",
        'data-avatar-container' => 'true',
        'data-user-initial' => $initial,
    ])); ?>

>
    <?php if($hasAvatar): ?>
        <img 
            src="<?php echo e($avatarUrl); ?>" 
            alt="<?php echo e($user->full_name ?: $displayName); ?>"
            class="w-full h-full object-cover"
            style="<?php echo e($focusStyle); ?>"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        >
        
        <span class="font-semibold text-slate-600 hidden items-center justify-center w-full h-full absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 text-white">
            <?php echo e($initial); ?>

        </span>
    <?php else: ?>
        
        <span class="font-semibold text-white flex items-center justify-center w-full h-full bg-gradient-to-br from-blue-500 to-blue-600">
            <?php echo e($initial); ?>

        </span>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\JAVADEV\resources\views/components/avatar.blade.php ENDPATH**/ ?>