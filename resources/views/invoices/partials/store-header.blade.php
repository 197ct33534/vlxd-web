<section aria-label="Thông tin cửa hàng" class="flex items-start gap-3 border-b border-border-light bg-primary/5 px-4 py-4 dark:border-border-dark dark:bg-primary/10 sm:px-6">
    <span aria-hidden="true" class="material-symbols-outlined shrink-0 rounded-lg bg-primary/10 p-2 text-2xl text-primary print:hidden">storefront</span>
    <div class="min-w-0 space-y-1">
        <p class="break-words text-lg font-bold text-gray-900 dark:text-white">{{ $storeInfo->name }}</p>
        @if(filled($storeInfo->address))
            <p class="break-words text-sm text-gray-600 dark:text-gray-300">{{ $storeInfo->address }}</p>
        @endif
        @if(filled($storeInfo->phone))
            <p class="text-sm text-gray-600 dark:text-gray-300">
                Điện thoại
                @if($storeInfo->telHref())
                    <a href="{{ $storeInfo->telHref() }}" class="font-semibold text-primary underline">{{ $storeInfo->phone }}</a>
                @else
                    {{ $storeInfo->phone }}
                @endif
            </p>
        @endif
    </div>
</section>
