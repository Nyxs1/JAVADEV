@props([
    'user',
    'size' => 'md',
    'shape' => 'circle',
    'class' => '',
])

@php
    $sizeClasses = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-16 h-16 text-xl',
        'xl' => 'w-24 h-24 text-3xl',
        '2xl' => 'w-32 h-32 text-4xl',
        'banner' => 'w-full aspect-video max-h-72',
    ];

    $shapeClasses = [
        'circle' => 'rounded-full',
        'rounded' => 'rounded-xl',
        'rounded-lg' => 'rounded-2xl',
        'square' => 'rounded-lg',
        'banner' => 'rounded-none',
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $shapeClass = $shapeClasses[$shape] ?? $shapeClasses['circle'];

    $hasAvatar = $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar);
    $displayName = $user->username ?? $user->name ?? 'U';
    $initial = strtoupper(substr($displayName, 0, 1));
    $avatarUrl = $hasAvatar ? $user->avatar_url : null;

    $isCurrentUser = auth()->check() && auth()->id() === $user->id;

    // Helper to build style string (same formula as User::getAvatarStyleAttribute)
    $buildStyle = function (float $zoom, float $panX, float $panY): string {
        $posX = 50 - ($panX * 50);
        $posY = 50 - ($panY * 50);

        $posX = max(0, min(100, $posX));
        $posY = max(0, min(100, $posY));

        return "object-position: {$posX}% {$posY}%; transform: scale({$zoom}); transform-origin: {$posX}% {$posY}%;";
    };

    $focus = method_exists($user, 'getAvatarFocusWithDefaults')
        ? $user->getAvatarFocusWithDefaults()
        : ($user->avatar_focus ?? ['zoom' => 1, 'panX' => 0, 'panY' => 0, 'frame' => 'circle']);

    $zoom = (float) ($focus['zoom'] ?? 1.0);
    $panX = (float) ($focus['panX'] ?? 0.0);
    $panY = (float) ($focus['panY'] ?? 0.0);
    $frame = $focus['frame'] ?? 'circle';

    /**
     * KEY FIX:
     * - frame='circle' means the image was pre-cropped from the circle overlay area
     *   during onboarding. NO transforms needed - just display as-is with object-cover.
     * - frame='banner' is LEGACY data that needs conversion.
     */
    $focusStyle = '';
    
    if ($frame === 'banner' && $shape === 'circle') {
        // Legacy banner data: apply conversion and transforms
        $panX = $panX * 3.0;
        $panX = max(-1.0, min(1.0, $panX));
        $focusStyle = $buildStyle($zoom, $panX, $panY);
    } elseif ($frame === 'banner') {
        // Legacy banner displayed as banner - apply as-is
        $focusStyle = $buildStyle($zoom, $panX, $panY);
    }
    // For frame='circle': leave $focusStyle empty - image is already correctly cropped
@endphp

<div
    {{ $attributes->merge([
        'class' => "avatar-container {$sizeClass} {$shapeClass} {$class} bg-slate-200 flex items-center justify-center overflow-hidden relative",
        'data-avatar-container' => 'true',
        'data-user-initial' => $initial,
        'data-user-id' => $user->id,
    ]) }}
>
    @if($hasAvatar)
        <img
            src="{{ $avatarUrl }}"
            alt="{{ $user->full_name ?: $displayName }}"
            class="w-full h-full object-cover"
            style="{{ $focusStyle }}"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            @if($isCurrentUser) data-avatar-img="user-{{ $user->id }}" data-avatar-shape="{{ $shape }}" @endif
        >
        <span
            class="font-semibold text-slate-600 hidden items-center justify-center w-full h-full absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 text-white"
            @if($isCurrentUser) data-avatar-fallback="user-{{ $user->id }}" @endif
        >
            {{ $initial }}
        </span>
    @else
        <span
            class="font-semibold text-white flex items-center justify-center w-full h-full bg-gradient-to-br from-blue-500 to-blue-600"
            @if($isCurrentUser) data-avatar-fallback="user-{{ $user->id }}" @endif
        >
            {{ $initial }}
        </span>
    @endif
</div>
