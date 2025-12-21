@props(['variant' => 'primary', 'type' => 'button'])

@php
    $classes = match($variant) {
        'primary' => 'bg-primary text-white hover:bg-primary/90',
        'secondary' => 'bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark',
        default => ''
    };
@endphp

<button {{ $attributes->merge([
    'type' => $type,
    'class' => 'flex items-center justify-center gap-2 rounded-lg h-10 px-4 text-sm font-bold shadow-subtle transition-colors ' . $classes
]) }}>
    {{ $slot }}
</button>
