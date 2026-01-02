@props([
    'type' => 'success',
    'message' => ''
])
@php
    $config = [
        'success' => [
            'bg' => 'bg-green-50 border-green-200',
            'text' => 'text-green-800',
            'icon' => asset('assets/icons/check-circle.svg'),
        ],
        'error' => [
            'bg' => 'bg-red-50 border-red-200',
            'text' => 'text-red-800',
            'icon' => asset('assets/icons/x-circle.svg'),
        ],
    ];
    $style = $config[$type] ?? $config['success'];
@endphp

@if($message)
    <div class="mb-6 p-4 {{ $style['bg'] }} border rounded-lg">
        <div class="flex items-center gap-3">
            <img src="{{ $style['icon'] }}" alt="" class="w-5 h-5">
            <span class="{{ $style['text'] }}">{{ $message }}</span>
        </div>
    </div>
@endif
