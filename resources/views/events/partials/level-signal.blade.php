{{-- Level Signal Bars (1-4) --}}
@php
    $level ??= 1;
    $levelLabels = [
        1 => 'Beginner',
        2 => 'Fundamental',
        3 => 'Intermediate',
        4 => 'Advanced',
    ];
    $levelLabel = $levelLabels[$level] ?? '';
@endphp
<div class="level-signal" data-tooltip="{{ $levelLabel }}">
    @for($i = 1; $i <= 4; $i++)
        <span class="level-signal__bar {{ $i <= $level ? 'level-signal__bar--active' : '' }}"></span>
    @endfor
    <span class="level-signal__tooltip">{{ $levelLabel }}</span>
</div>
