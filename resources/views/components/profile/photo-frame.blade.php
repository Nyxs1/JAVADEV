{{--
    Profile Cover Component - HORIZONTAL COVER (Banner-like)
    Reusable across Profile, Onboarding, Settings
    
    Props:
    - $src: Image source URL (optional)
    - $alt: Alt text for image (optional)
    - $fallbackInitial: Single letter to show when no image
    - $showEditor: Whether to show editor features (default: false)
    - $hasImage: Whether an image is currently set
--}}

@props([
    'src' => null,
    'alt' => 'Profile Cover',
    'fallbackInitial' => '?',
    'showEditor' => false,
    'hasImage' => false,
    'style' => null,
    'user' => null,
])

{{-- 
    HORIZONTAL COVER: aspect-[3/1] (e.g. 768x256)
    This is the SOURCE OF TRUTH for photo display
--}}
<div class="w-full aspect-[3/1] rounded-2xl bg-slate-100 overflow-hidden shadow-md relative group
    @if($showEditor) border-2 border-dashed border-slate-300 hover:border-blue-400 cursor-pointer hover:shadow-lg transition-all duration-300 @endif"
    @if($showEditor) id="avatar-dropzone" style="touch-action: none;" @endif>

    {{-- 1. Blurred Background Layer (Fills empty space when zoomed out) --}}
    {{-- Scale 125% to ensure full coverage + object-cover --}}
    <div id="avatar-blur-bg-container" class="absolute inset-0 pointer-events-none {{ ($src || $hasImage) ? '' : 'hidden' }}">
        @if($src)
            <img id="avatar-blur-img" src="{{ $src }}" alt="" 
                class="w-full h-full object-cover blur-2xl scale-125 opacity-60">
        @else
            <img id="avatar-blur-img" src="" alt="" 
                class="w-full h-full object-cover blur-2xl scale-125 opacity-60 hidden">
        @endif
        {{-- Vignette overlay to soften edges --}}
        <div class="absolute inset-0 bg-gradient-to-b from-slate-900/20 via-transparent to-slate-900/40"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/20 via-transparent to-slate-900/20"></div>
    </div>

    @if($showEditor)
        {{-- 2. Editor Mode: Image Container (for pan/zoom) --}}
        <div id="avatar-img-container" class="absolute inset-0 overflow-hidden rounded-2xl {{ $hasImage ? '' : 'hidden' }}">
            {{-- 
                IMPORTANT: NO w-full h-full object-cover classes!
                JS controls all dimensions via transform.
                Image is centered with left:50% top:50% and translate(-50%, -50%)
            --}}
            <img id="avatar-img" src="{{ $src ?? '' }}" alt="{{ $alt }}"
                class="absolute select-none"
                style="left: 50%; top: 50%; transform-origin: center; max-width: none; max-height: none;"
                draggable="false">
        </div>

        {{-- 3. CENTER CIRCLE OVERLAY - Shows navbar avatar extraction area (EDITOR ONLY) --}}
        <div id="avatar-circle-mask" class="absolute inset-0 pointer-events-none {{ $hasImage ? '' : 'hidden' }}">
            {{-- Translucent gray overlay with transparent circle at CENTER --}}
            {{-- Circle radius 115 = large, nearly full height (banner height 256, max radius ~128) --}}
            <svg class="w-full h-full" viewBox="0 0 768 256" preserveAspectRatio="xMidYMid slice">
                <defs>
                    <mask id="center-circle-mask-{{ uniqid() }}">
                        <rect width="100%" height="100%" fill="white"/>
                        <circle cx="384" cy="128" r="115" fill="black"/>
                    </mask>
                </defs>
                <rect width="100%" height="100%" fill="rgba(0,0,0,0.35)" mask="url(#center-circle-mask-{{ uniqid() }})"/>
            </svg>
            
            {{-- Dashed circle border (92% of container height = big circle) --}}
            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 aspect-square h-[92%] rounded-full border-2 border-white/80 border-dashed shadow-lg"></div>
            
            {{-- Label --}}
            <div class="absolute left-1/2 bottom-1 -translate-x-1/2 bg-black/70 text-white text-[10px] px-2.5 py-1 rounded-full whitespace-nowrap backdrop-blur-sm">
                Avatar Area
            </div>
        </div>

        {{-- 4. Empty State Placeholder --}}
        <div id="avatar-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 {{ $hasImage ? 'hidden' : '' }}">
            <div class="w-16 h-16 rounded-full bg-slate-200 flex items-center justify-center mb-3 border-2 border-dashed border-slate-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-sm font-medium mb-1">Upload Cover Photo</span>
            <span class="text-xs text-slate-400">Drag & drop or click</span>
        </div>

        {{-- 5. Drop Overlay --}}
        <div id="avatar-drop-overlay"
            class="absolute inset-0 bg-blue-500/90 rounded-2xl flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-200 z-10">
            <svg class="w-12 h-12 text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span class="text-white text-sm font-medium">Drop image here!</span>
        </div>

        {{-- 6. Edit Mode Hint --}}
        <div id="avatar-edit-overlay"
            class="absolute top-3 left-1/2 -translate-x-1/2 bg-black/70 text-white text-xs px-4 py-2 rounded-full opacity-0 transition-opacity duration-200 pointer-events-none flex items-center gap-2 z-10 backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
            </svg>
            <span>Drag to pan • Scroll to zoom</span>
        </div>
    @else
        {{-- VIEW MODE: Render image with proper pan/zoom from avatar_focus --}}
        @if($src)
            @php
                // Parse zoom and pan from the avatar_style string
                // avatar_style format: "object-position: X% Y%; transform: scale(Z); transform-origin: center;"
                $zoom = 1;
                $posX = 50;
                $posY = 50;
                
                if ($style) {
                    if (preg_match('/object-position:\s*([\d.]+)%\s*([\d.]+)%/', $style, $posMatches)) {
                        $posX = floatval($posMatches[1]);
                        $posY = floatval($posMatches[2]);
                    }
                    if (preg_match('/scale\(([\d.]+)\)/', $style, $scaleMatch)) {
                        $zoom = floatval($scaleMatch[1]);
                    }
                }
            @endphp
            {{-- Simple approach: object-position handles pan, scale handles zoom --}}
            {{-- Use a wrapper with overflow:hidden to clip zoomed content --}}
            @php
                $isCurrentUser = $user && auth()->check() && auth()->id() === $user->id;
            @endphp
            <div class="absolute inset-0 overflow-hidden">
                <img src="{{ $src }}" alt="{{ $alt }}" 
                    class="absolute inset-0 w-full h-full object-cover"
                    style="object-position: {{ $posX }}% {{ $posY }}%;{{ $zoom != 1 ? ' transform: scale(' . $zoom . '); transform-origin: ' . $posX . '% ' . $posY . '%;' : '' }}"
                    @if($isCurrentUser) data-avatar-img="user-{{ $user->id }}" data-avatar-shape="banner" @endif>
            </div>
        @else
            <div class="absolute inset-0 flex items-center justify-center w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600">
                <span class="text-6xl font-bold text-white/60">{{ strtoupper($fallbackInitial) }}</span>
            </div>
        @endif
    @endif
</div>
