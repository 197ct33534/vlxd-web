@props(['color' => 'green'])

@php
    $colors = [
        'green' => 'text-green-800 bg-green-100',
        'red' => 'text-red-800 bg-red-100',
    ];
@endphp

<span class="px-2 py-1 text-xs font-semibold {{ $colors[$color] ?? $colors['green'] }} rounded-full">
    {{ $slot }}
</span>
