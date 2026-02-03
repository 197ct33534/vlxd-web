@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2">
                    <a href="{{ route('customers.projects.index', $project->customer_id) }}" class="hover:text-primary transition-colors">Projects</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('projects.invoices.index', $project->id) }}" class="hover:text-primary transition-colors">{{ $project->name }}</a>
                    <span class="mx-2">/</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">Price History</span>
                </nav>
                <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Product Price History</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Track price changes of products over time.</p>
            </div>
            
            <a href="{{ route('projects.invoices.index', $project->id) }}" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors text-sm font-semibold">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                Back to Invoices
            </a>
        </div>

        @if($history->isEmpty())
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700">
                <div class="bg-gray-100 dark:bg-gray-700 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl text-gray-400">history_edu</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No History Data</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">There are no invoices recorded for this project yet. Create an invoice to see price history.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6" x-data="{ searchQuery: '' }">
                <!-- Simple Filter -->
                <div class="bg-container-light dark:bg-container-dark p-4 rounded-xl shadow-subtle sticky top-4 z-10">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                        <input x-model="searchQuery" type="text" placeholder="Filter products by name..." 
                               class="w-full pl-12 pr-4 py-3 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                </div>

                @foreach($history as $key => $items)
                    @php
                        [$productName, $unit, $quantity] = explode('|', $key);
                    @endphp
                    <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden" 
                         x-show="searchQuery === '' || '{{ strtolower($productName) }}'.includes(searchQuery.toLowerCase())">
                        <div class="p-6 border-b border-border-light dark:border-border-dark bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                            <h2 class="text-xl font-bold text-text-light dark:text-text-dark flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">inventory_2</span>
                                {{ $productName }}
                            </h2>
                            <div class="flex gap-4 text-sm text-gray-500 font-medium">
                                <span class="bg-white dark:bg-gray-700 px-3 py-1 rounded border border-gray-200 dark:border-gray-600">
                                    Qty: {{ $quantity }}
                                </span>
                                <span class="bg-white dark:bg-gray-700 px-3 py-1 rounded border border-gray-200 dark:border-gray-600">
                                    Unit: {{ $unit }}
                                </span>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Date</th>
                                        <th class="px-6 py-3">Invoice</th>
                                        <th class="px-6 py-3 text-right">Price</th>
                                        <th class="px-6 py-3 text-right">Change</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @php 
                                        $previousPrice = null; 
                                        $items = $items->sortBy('invoice_date'); 
                                    @endphp
                                    @foreach($items as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($item->invoice_date)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <a href="#" @click="$dispatch('open-invoice-modal', { id: {{ $item->invoice_id }} })" class="text-blue-600 hover:underline">
                                                    #{{ $item->invoice_code ?? $item->invoice_id }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-right font-bold">
                                                {{ number_format($item->price, 0, ',', '.') }} đ
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($previousPrice !== null)
                                                    @if($item->price > $previousPrice)
                                                        <div class="inline-flex items-center text-red-600 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded text-xs font-bold">
                                                            <span class="material-symbols-outlined text-sm mr-1">trending_up</span>
                                                            +{{ number_format($item->price - $previousPrice, 0, ',', '.') }}
                                                        </div>
                                                    @elseif($item->price < $previousPrice)
                                                        <div class="inline-flex items-center text-green-600 bg-green-50 dark:bg-green-900/30 px-2 py-1 rounded text-xs font-bold">
                                                            <span class="material-symbols-outlined text-sm mr-1">trending_down</span>
                                                            -{{ number_format($previousPrice - $item->price, 0, ',', '.') }}
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-xs">—</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 text-xs text-italic">Initial</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $previousPrice = $item->price; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Reusing Invoice Detail Modal -->
    <div x-data="{ open: false, content: '', loading: false }"
         @open-invoice-modal.window="
            open = true; 
            loading = true; 
            content = '';
            fetch('/invoices/' + $event.detail.id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => { content = html; loading = false; })
                .catch(() => { content = 'Error loading invoice.'; loading = false; });
         "
         class="relative z-50" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         x-show="open" 
         style="display: none;">
        
        <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex justify-end absolute top-4 right-4 z-10">
                            <button @click="open = false" class="text-gray-400 hover:text-gray-500">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        <div x-show="loading" class="flex justify-center py-12">
                            <span class="material-symbols-outlined animate-spin text-4xl text-primary">progress_activity</span>
                        </div>
                        <div x-show="!loading" x-html="content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
