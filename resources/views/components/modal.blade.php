@props(['name', 'title' => '', 'width' => 'max-w-lg'])

<div
    x-cloak
    x-show="{{ $name }}"
    x-on:keydown.escape.window="{{ $name }} = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-end="opacity-100"
    x-transition:enter-start="opacity-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-end="opacity-0"
    x-transition:leave-start="opacity-100"
>
    <div
        class="w-full {{ $width }} bg-container-light dark:bg-container-dark rounded-xl shadow-lg p-6"
        x-show="{{ $name }}"
        @click.outside="{{ $name }} = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
    >
        <div class="flex items-center justify-between pb-4 border-b border-border-light dark:border-border-dark mb-6">
            <h3 class="text-xl font-bold text-text-light dark:text-text-dark">
                @if(isset($header))
                    {{ $header }}
                @else
                    {{ $title }}
                @endif
            </h3>
            <button type="button" @click="{{ $name }} = false" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-700 dark:hover:text-gray-100 transition-colors">
                <span class="material-symbols-outlined text-xl leading-none">close</span>
                <span>{{ __('common.close') }}</span>
            </button>
        </div>
        
        {{ $slot }}
    </div>
</div>
