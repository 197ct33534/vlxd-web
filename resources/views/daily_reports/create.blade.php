@extends('layouts.dashboard')

@section('title', __('daily_reports.create.title'))

@section('content')
<div class="max-w-4xl mx-auto" x-data="dailyReportForm()">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-2xl font-bold tracking-tight">{{ __('daily_reports.create.title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('daily_reports.create.subtitle') }}</p>
        </div>
        <a href="{{ route('daily-reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-1 text-sm font-medium">
            <span class="material-symbols-outlined text-lg">arrow_back</span> {{ __('daily_reports.common.back') }}
        </a>
    </div>

    <form action="{{ route('daily-reports.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Info -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('daily_reports.create.reporter') }}</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled class="bg-gray-100 dark:bg-gray-700 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:text-gray-400 text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('daily_reports.table.date') }}</label>
                    <input type="date" name="report_date" value="{{ date('Y-m-d') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('daily_reports.create.delivery_items') }}</h3>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 relative group">
                        <!-- Remove Button -->
                        <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" title="{{ __('daily_reports.create.remove_item') }}">
                            <span class="material-symbols-outlined">close</span>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Project -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('daily_reports.table.project') }}</label>
                                <select :name="'items[' + index + '][project_id]'" x-model="item.project_id" @change="fetchPrice(index)" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="">{{ __('daily_reports.create.select_project') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('daily_reports.table.product') }}</label>
                                <input type="text" :name="'items[' + index + '][product_name]'" x-model="item.product_name" @blur="fetchPrice(index)" required placeholder="{{ __('daily_reports.create.placeholder_product') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Unit -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('common.unit') }}</label>
                                <input type="text" :name="'items[' + index + '][unit]'" x-model="item.unit" required placeholder="kg/bao" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Quantity -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('common.quantity') }}</label>
                                <input type="number" step="0.01" :name="'items[' + index + '][quantity]'" x-model="item.quantity" required min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Price -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    {{ __('common.price') }} 
                                    <template x-if="item.isFetching"><span class="animate-pulse text-primary ml-1">...</span></template>
                                </label>
                                <input type="number" :name="'items[' + index + '][price]'" x-model="item.price" required min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-6 pt-5 border-t border-gray-200 dark:border-gray-700 flex justify-center sm:justify-start">
                <button
                    type="button"
                    @click="addItem()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border-2 border-dashed border-primary/40 bg-primary/5 hover:bg-primary/10 text-primary px-4 py-3 text-sm font-semibold w-full sm:w-auto transition-colors"
                >
                    <span class="material-symbols-outlined text-xl">add_circle</span>
                    {{ __('daily_reports.create.add_row') }}
                </button>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg shadow-md font-semibold transition-all transform hover:-translate-y-0.5">
                <span class="material-symbols-outlined text-xl">send</span>
                {{ __('daily_reports.create.submit') }}
            </button>
        </div>
    </form>
</div>

<script>
    function dailyReportForm() {
        return {
            items: [
                { project_id: '', product_name: '', unit: '', quantity: 1, price: 0, isFetching: false }
            ],

            addItem() {
                this.items.push({ project_id: '', product_name: '', unit: '', quantity: 1, price: 0, isFetching: false });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            async fetchPrice(index) {
                let item = this.items[index];
                if (!item.project_id || !item.product_name) return;

                item.isFetching = true;
                try {
                    let url = `{{ route('api.latest-price') }}?project_id=${item.project_id}&product_name=${encodeURIComponent(item.product_name)}`;
                    let res = await fetch(url);
                    let data = await res.json();
                    
                    if (data.price) {
                        item.price = data.price;
                    }
                } catch (e) {
                    console.error("Failed to fetch price", e);
                } finally {
                    item.isFetching = false;
                }
            }
        }
    }
</script>
@endsection
