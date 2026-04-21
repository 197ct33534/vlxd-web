@php
    $hasActiveFilters = request()->filled('search')
        || (request('status') && request('status') !== 'all')
        || in_array(request('has_projects'), ['yes', 'no'], true)
        || (request('sort') && request('sort') !== 'newest')
        || (request('per_page') && (int) request('per_page') !== 15);
@endphp

<div class="mb-6 rounded-2xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark"
     x-data="{
        search: @js(request('search', '')),
        status: @js(request('status', 'all')),
        sort: @js(request('sort', 'newest')),
        has_projects: @js(request('has_projects') ?: ''),
        per_page: @js((string) request('per_page', 15)),
        clearSearchAndSubmit() {
            this.search = '';
            this.$nextTick(() => {
                if (this.$refs.form) {
                    this.$refs.form.submit();
                }
            });
        },
        resetFilters() {
            window.location.href = @json(route('customers.index'));
        }
     }">
    <div class="border-b border-border-light px-4 py-3 dark:border-border-dark sm:px-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">filter_alt</span>
                <span class="text-sm font-bold text-text-light dark:text-text-dark">{{ __('customer.filter.panel_title') }}</span>
                @if($hasActiveFilters)
                    <span class="rounded-full bg-primary/15 px-2 py-0.5 text-xs font-semibold text-primary">{{ __('customer.filter.active_badge') }}</span>
                @endif
            </div>
            @if($hasActiveFilters)
                <button type="button" @click="resetFilters()" class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <span class="material-symbols-outlined text-base">restart_alt</span>
                    {{ __('customer.filter.reset') }}
                </button>
            @endif
        </div>
    </div>

    <form x-ref="form" action="{{ route('customers.index') }}" method="GET" class="space-y-4 p-4 sm:p-5">
        {{-- Tìm kiếm --}}
        <div class="relative">
            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('common.search') }}</label>
            <div class="relative flex h-12 items-stretch">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500">search</span>
                <input x-model="search" name="search" type="search" autocomplete="off"
                       class="form-input w-full rounded-xl border-border-light bg-background-light pl-11 pr-11 text-base text-text-light focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark dark:text-text-dark"
                       placeholder="{{ __('customer.filter.search_placeholder') }}"
                       aria-label="{{ __('common.search') }}" />
                <button type="button" x-show="search && search.length > 0" x-cloak @click="clearSearchAndSubmit()"
                        class="absolute right-2 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-800 dark:hover:text-gray-200"
                        aria-label="{{ __('customer.filter.clear_search') }}"
                        title="{{ __('customer.filter.clear_search') }}">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
        </div>

        {{-- Lưới bộ lọc --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('customer.filter.status_heading') }}</label>
                <div class="relative">
                    <select x-model="status" name="status"
                            class="h-11 w-full appearance-none rounded-xl border border-border-light bg-background-light py-2 pl-3 pr-10 text-sm font-medium text-text-light focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark dark:text-text-dark">
                        <option value="all">{{ __('customer.filter.all') }}</option>
                        <option value="active">{{ __('customer.filter.active') }}</option>
                        <option value="inactive">{{ __('customer.filter.inactive') }}</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500">
                        <span class="material-symbols-outlined text-[20px]">expand_more</span>
                    </span>
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('customer.filter.sort_label') }}</label>
                <div class="relative">
                    <select x-model="sort" name="sort"
                            class="h-11 w-full appearance-none rounded-xl border border-border-light bg-background-light py-2 pl-3 pr-10 text-sm font-medium text-text-light focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark dark:text-text-dark">
                        <option value="newest">{{ __('customer.filter.sort_newest') }}</option>
                        <option value="oldest">{{ __('customer.filter.sort_oldest') }}</option>
                        <option value="name_asc">{{ __('customer.filter.sort_name_asc') }}</option>
                        <option value="name_desc">{{ __('customer.filter.sort_name_desc') }}</option>
                        <option value="debt_desc">{{ __('customer.filter.sort_debt_desc') }}</option>
                        <option value="projects_desc">{{ __('customer.filter.sort_projects_desc') }}</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500">
                        <span class="material-symbols-outlined text-[20px]">expand_more</span>
                    </span>
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('customer.filter.has_projects_label') }}</label>
                <div class="relative">
                    <select x-model="has_projects" name="has_projects"
                            class="h-11 w-full appearance-none rounded-xl border border-border-light bg-background-light py-2 pl-3 pr-10 text-sm font-medium text-text-light focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark dark:text-text-dark">
                        <option value="">{{ __('customer.filter.projects_all') }}</option>
                        <option value="yes">{{ __('customer.filter.projects_yes') }}</option>
                        <option value="no">{{ __('customer.filter.projects_no') }}</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500">
                        <span class="material-symbols-outlined text-[20px]">expand_more</span>
                    </span>
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('customer.filter.per_page_label') }}</label>
                <div class="relative">
                    <select x-model="per_page" name="per_page"
                            class="h-11 w-full appearance-none rounded-xl border border-border-light bg-background-light py-2 pl-3 pr-10 text-sm font-medium text-text-light focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark dark:text-text-dark">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-gray-500">
                        <span class="material-symbols-outlined text-[20px]">expand_more</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 border-t border-border-light pt-4 dark:border-border-dark sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400 sm:max-w-xl">
                {{ __('customer.filter.hint') }}
            </p>
            <div class="flex w-full shrink-0 flex-wrap items-center justify-end gap-2 sm:w-auto">
                <button
                    type="button"
                    x-show="search && search.trim().length > 0"
                    x-cloak
                    @click="clearSearchAndSubmit()"
                    class="inline-flex h-11 min-w-[120px] items-center justify-center gap-2 rounded-xl border border-border-light bg-background-light px-4 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-border-dark dark:bg-background-dark dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    <span class="material-symbols-outlined text-base">close</span>
                    {{ __('customer.filter.clear_search') }}
                </button>
                <button type="submit" class="inline-flex h-11 min-w-[140px] items-center justify-center gap-2 rounded-xl bg-primary px-6 text-sm font-bold text-white shadow-subtle transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <span class="material-symbols-outlined text-base">search</span>
                    {{ __('customer.filter.apply') }}
                </button>
            </div>
        </div>
    </form>
</div>
