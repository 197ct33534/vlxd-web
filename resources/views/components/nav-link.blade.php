@props(['href', 'icon', 'active' => false])
<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2 rounded-lg text-text-light dark:text-text-dark hover:bg-primary/10 transition-colors {{ $active ? 'bg-primary/20 text-primary' : '' }}"
   {{ $attributes }}>
    <span class="material-symbols-outlined">{{ $icon }}</span>
    <span class="text-sm font-medium">{{ $slot }}</span>
</a>
