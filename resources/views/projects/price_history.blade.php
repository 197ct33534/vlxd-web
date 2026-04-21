@extends('layouts.dashboard')

@section('title', __('invoices.price_history_title').' — '.$project->name)

@section('page_heading', __('invoices.price_history_title'))

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="mb-2 flex flex-wrap gap-x-2 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('customers.projects.index', $project->customer_id) }}" class="hover:text-primary transition-colors">{{ __('nav.customers') }}</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('projects.invoices.index', $project->id) }}" class="hover:text-primary transition-colors">{{ $project->name }}</a>
                    <span class="text-gray-400">/</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('invoices.price_history') }}</span>
                </nav>
                <h1 class="text-3xl font-black tracking-tight text-text-light dark:text-text-dark">{{ __('invoices.price_history_title') }}</h1>
                <p class="mt-1 max-w-3xl text-gray-500 dark:text-gray-400">{{ __('invoices.price_history_subtitle') }}</p>
            </div>

            <a href="{{ route('projects.invoices.index', $project->id) }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                {{ __('invoices.price_history.back') }}
            </a>
        </div>

        @if(!$hasInvoices)
            <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 p-12 text-center dark:border-gray-700 dark:bg-gray-800/50">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                    <span class="material-symbols-outlined text-3xl text-gray-400">receipt_long</span>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">{{ __('invoices.price_history.empty_title') }}</h3>
                <p class="mx-auto max-w-md text-gray-500 dark:text-gray-400">{{ __('invoices.price_history.empty_no_invoices') }}</p>
            </div>
        @elseif(!$hasLineItems)
            <div class="rounded-xl border-2 border-dashed border-amber-200 bg-amber-50/80 p-12 text-center dark:border-amber-900/40 dark:bg-amber-950/20">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <span class="material-symbols-outlined text-3xl text-amber-600 dark:text-amber-400">inventory_2</span>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">{{ __('invoices.price_history.empty_title') }}</h3>
                <p class="mx-auto max-w-md text-gray-600 dark:text-gray-400">{{ __('invoices.price_history.empty_no_lines') }}</p>
            </div>
        @else
            <div
                class="flex flex-col gap-4"
                x-data="{
                    searchQuery: '',
                    matchesProduct(name) {
                        const q = this.searchQuery.trim().toLowerCase();
                        if (!q) return true;
                        return name.toLowerCase().includes(q);
                    }
                }"
            >
                <div class="sticky top-4 z-10 rounded-xl bg-container-light p-4 shadow-subtle dark:bg-container-dark">
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input
                            x-model="searchQuery"
                            type="search"
                            autocomplete="off"
                            placeholder="{{ __('invoices.price_history.filter_placeholder') }}"
                            class="w-full rounded-lg border border-gray-200 py-3 pl-12 pr-4 transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20 dark:border-gray-700 dark:bg-gray-800/80"
                            aria-label="{{ __('common.search') }}"
                        />
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px] text-left text-sm">
                            <thead class="border-b border-border-light bg-background-light text-xs font-bold uppercase tracking-wide text-gray-600 dark:border-border-dark dark:bg-background-dark dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 sm:px-6">{{ __('invoices.price_history.table.product') }}</th>
                                    <th class="px-4 py-3 sm:px-6">{{ __('invoices.price_history.table.unit') }}</th>
                                    <th class="px-4 py-3 text-right sm:px-6">{{ __('invoices.price_history.table.latest_price') }}</th>
                                    <th class="px-4 py-3 sm:px-6">{{ __('invoices.price_history.table.last_invoice') }}</th>
                                    <th class="whitespace-nowrap px-4 py-3 sm:px-6">{{ __('invoices.price_history.table.last_date') }}</th>
                                    <th class="px-4 py-3 text-center sm:px-6">{{ __('invoices.price_history.table.variation') }}</th>
                                    <th class="min-w-[220px] px-4 py-3 sm:px-6">{{ __('invoices.price_history.table.detail') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border-light dark:divide-border-dark">
                                @foreach($summaries as $row)
                                    <tr
                                        class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                                        x-show="matchesProduct(@js($row->product_name))"
                                    >
                                        <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white sm:px-6">
                                            {{ $row->product_name }}
                                        </td>
                                        <td class="px-4 py-4 text-gray-700 dark:text-gray-300 sm:px-6">
                                            {{ $row->unit !== '' ? $row->unit : '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-base font-bold tabular-nums text-gray-900 dark:text-white sm:px-6">
                                            {{ number_format($row->latest_price, 0, ',', '.') }} đ
                                        </td>
                                        <td class="px-4 py-4 sm:px-6">
                                            <a href="{{ route('invoices.show', $row->latest_invoice_id) }}" class="font-medium text-primary hover:underline">
                                                {{ $row->latest_invoice_code ?: '#'.$row->latest_invoice_id }}
                                            </a>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-4 text-gray-700 dark:text-gray-300 sm:px-6">
                                            {{ $row->latest_date ? \Carbon\Carbon::parse($row->latest_date)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-center sm:px-6">
                                            @if($row->has_variation)
                                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
                                                    {{ __('invoices.price_history.variation_yes') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                                    {{ __('invoices.price_history.variation_no') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 sm:px-6">
                                            @if(!$row->has_variation)
                                                <span class="text-gray-500 dark:text-gray-400">
                                                    {{ $row->line_count <= 1 ? __('invoices.price_history.detail_once') : __('invoices.price_history.detail_fixed') }}
                                                </span>
                                            @else
                                                @if($row->direction === 'up')
                                                    <span class="inline-flex items-center gap-1 font-semibold text-red-600 dark:text-red-400">
                                                        <span class="material-symbols-outlined text-lg">trending_up</span>
                                                        {{ __('invoices.price_history.detail_up', ['amount' => number_format($row->abs_change, 0, ',', '.')]) }}
                                                    </span>
                                                @elseif($row->direction === 'down')
                                                    <span class="inline-flex items-center gap-1 font-semibold text-emerald-600 dark:text-emerald-400">
                                                        <span class="material-symbols-outlined text-lg">trending_down</span>
                                                        {{ __('invoices.price_history.detail_down', ['amount' => number_format($row->abs_change, 0, ',', '.')]) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-700 dark:text-gray-300" title="{{ __('invoices.price_history.detail_same_vs_prev_hint') }}">
                                                        {{ __('invoices.price_history.detail_same_vs_prev') }}
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
