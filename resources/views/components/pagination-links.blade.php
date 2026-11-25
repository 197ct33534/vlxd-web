@if ($paginator->hasPages())
    <nav aria-label="{{ $navigationLabel ?? 'Phân trang' }}" class="flex flex-wrap items-center justify-center gap-2 py-3 text-sm">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="rounded-lg border border-border-light px-3 py-2 text-gray-500 dark:border-border-dark">Trang trước</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="rounded-lg border border-border-light px-3 py-2 font-semibold text-primary hover:bg-primary/10 dark:border-border-dark">Trang trước</a>
        @endif

        <span class="px-2 font-medium text-gray-600 dark:text-gray-300 sm:hidden">
            Trang {{ $paginator->currentPage() }}/{{ $paginator->lastPage() }}
        </span>
        <div class="hidden flex-wrap items-center justify-center gap-1 sm:flex">
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <span aria-current="page" aria-label="Trang {{ $page }}" class="rounded-lg bg-primary px-3 py-2 font-bold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="Trang {{ $page }}" class="rounded-lg px-3 py-2 text-primary hover:bg-primary/10">{{ $page }}</a>
                        @endif
                    @endforeach
                @else
                    <span class="px-2 text-gray-500">{{ $element }}</span>
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="rounded-lg border border-border-light px-3 py-2 font-semibold text-primary hover:bg-primary/10 dark:border-border-dark">Trang sau</a>
        @else
            <span aria-disabled="true" class="rounded-lg border border-border-light px-3 py-2 text-gray-500 dark:border-border-dark">Trang sau</span>
        @endif
    </nav>
@endif
