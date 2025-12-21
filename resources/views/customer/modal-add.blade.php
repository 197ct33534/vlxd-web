@props(['label', 'icon' => null, 'id' => null, 'type' => 'text', 'required' => false])

<label class="flex flex-col">
    @if($label)
        <span class="text-sm font-medium text-text-light dark:text-text-dark mb-1">{{ $label }}</span>
    @endif
    <div class="relative">
        @if($icon)
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">{{ $icon }}</span>
        @endif
        <input {{ $attributes->merge([
                'type' => $type,
                'id' => $id,
                'required' => $required,
                'class' => 'form-input rounded-lg border border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark h-10 px-3' . ($icon ? ' pl-12' : '')
            ]) }} />
    </div>
</label>
