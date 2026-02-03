@extends('layouts.dashboard')

@section('title', 'New Daily Report')

@section('content')
<div class="max-w-4xl mx-auto" x-data="dailyReportForm()">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-2xl font-bold tracking-tight">New Daily Report</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Submit your deliveries for today.</p>
        </div>
        <a href="{{ route('daily-reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 flex items-center gap-1 text-sm font-medium">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Back
        </a>
    </div>

    <form action="{{ route('daily-reports.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- General Info -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reporter</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled class="bg-gray-100 dark:bg-gray-700 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:text-gray-400 text-sm cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" name="report_date" value="{{ date('Y-m-d') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-800 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Delivery Items</h3>
                <button type="button" @click="addItem()" class="text-primary hover:text-blue-600 text-sm font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-lg">add</span> Add Item
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800/50 relative group">
                        <!-- Remove Button -->
                        <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors" title="Remove Item">
                            <span class="material-symbols-outlined">close</span>
                        </button>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <!-- Project -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Project</label>
                                <select :name="'items[' + index + '][project_id]'" x-model="item.project_id" @change="fetchPrice(index)" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Product -->
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Product</label>
                                <input type="text" :name="'items[' + index + '][product_name]'" x-model="item.product_name" @blur="fetchPrice(index)" required placeholder="e.g. Cement" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Unit -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Unit</label>
                                <input type="text" :name="'items[' + index + '][unit]'" x-model="item.unit" required placeholder="kg/bag" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Quantity -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Quantity</label>
                                <input type="number" step="0.01" :name="'items[' + index + '][quantity]'" x-model="item.quantity" required min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <!-- Price -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Price 
                                    <template x-if="item.isFetching"><span class="animate-pulse text-primary ml-1">...</span></template>
                                </label>
                                <input type="number" :name="'items[' + index + '][price]'" x-model="item.price" required min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-primary hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg shadow-md font-semibold transition-all transform hover:-translate-y-0.5">
                Submit Report
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
                        // Optional: Show toast or highlight that price was suggested
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
