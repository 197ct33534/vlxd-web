@extends('layouts.dashboard')

@section('title', 'Edit Invoice - ' . $invoice->project->name)

@section('content')
<div class="max-w-5xl mx-auto" x-data="invoiceForm()">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" class="text-gray-500 hover:text-primary transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Invoice #{{ $invoice->id }}</h1>
        </div>
        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this invoice? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="flex items-center gap-2 text-red-600 hover:text-red-700 font-semibold px-4 py-2 rounded-lg hover:bg-red-50 transition-colors">
                <span class="material-symbols-outlined">delete</span>
                Delete Invoice
            </button>
        </form>
    </div>

    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- General Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-subtle p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">General Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" name="invoice_date" required value="{{ $invoice->invoice_date }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Invoice Code (Optional)</label>
                    <input type="text" name="code" placeholder="e.g. INV-001" value="{{ $invoice->code }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note</label>
                    <input type="text" name="note" placeholder="Project notes..." value="{{ $invoice->note }}"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary focus:border-primary">
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-subtle p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Invoice Items</h2>
                <button type="button" @click="addItem()" class="flex items-center gap-1 text-sm font-semibold text-primary hover:text-blue-700">
                    <span class="material-symbols-outlined">add_circle</span>
                    Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Product Name</th>
                            <th class="px-4 py-3 w-32">Unit</th>
                            <th class="px-4 py-3 w-32 text-right">Quantity</th>
                            <th class="px-4 py-3 w-40 text-right">Price</th>
                            <th class="px-4 py-3 w-40 text-right">Amount</th>
                            <th class="px-4 py-3 w-16 rounded-r-lg text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-2">
                                    <input type="text" :name="`items[${index}][product_name]`" x-model="item.product_name" required placeholder="Item name"
                                           class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm font-medium placeholder-gray-400 dark:text-white">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" placeholder="kg, m..."
                                           class="w-full border-0 bg-transparent focus:ring-0 p-0 text-sm text-gray-600 dark:text-gray-300">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" required min="0" step="any"
                                           class="w-full border-0 bg-transparent focus:ring-0 p-0 text-right text-sm font-medium dark:text-white">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="`items[${index}][price]`" x-model="item.price" required min="0" step="any"
                                           class="w-full border-0 bg-transparent focus:ring-0 p-0 text-right text-sm font-medium dark:text-white">
                                </td>
                                <td class="px-4 py-2 text-right font-bold text-gray-900 dark:text-white">
                                    <span x-text="formatNumber(item.quantity * item.price)"></span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot class="border-t border-gray-200 dark:border-gray-700">
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-right font-bold text-lg">Total Amount:</td>
                            <td class="px-4 py-4 text-right font-black text-xl text-primary">
                                <span x-text="formatNumber(totalAmount)"></span> đ
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div x-show="items.length === 0" class="text-center py-8 text-gray-500 italic">
                No items added yet. Click "Add Item" to start.
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/30">
                Update Invoice
            </button>
        </div>
    </form>
</div>

<script>
    function invoiceForm() {
        return {
            items: @json($invoice->items).map(function(item) {
                return {
                    product_name: item.product_name,
                    unit: item.unit,
                    quantity: parseFloat(item.quantity),
                    price: parseFloat(item.price)
                };
            }),
            
            addItem() {
                this.items.push({ product_name: '', unit: '', quantity: 1, price: 0 });
            },
            
            removeItem(index) {
                this.items.splice(index, 1);
            },
            
            get totalAmount() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat('vi-VN').format(num);
            }
        }
    }
</script>
@endsection
