<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden print:shadow-none print:border-none">
    <!-- Invoice Header -->
    <div class="p-8 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-1">INVOICE</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm">#{{ $invoice->code ?? str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F, Y') }}</p>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-8">
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Customer / Project</p>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $invoice->project->customer->name ?? 'N/A' }}</h3>
                <p class="text-gray-600 dark:text-gray-300">{{ $invoice->project->name }}</p>
                <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">{{ $invoice->project->address }}</p>
            </div>
            @if($invoice->note)
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Note</p>
                <p class="text-gray-600 dark:text-gray-300 italic">{{ $invoice->note }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="p-8">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <th class="py-3 font-semibold">Description</th>
                    <th class="py-3 font-semibold text-center">Unit</th>
                    <th class="py-3 font-semibold text-center">Qty</th>
                    <th class="py-3 font-semibold text-right">Price</th>
                    <th class="py-3 font-semibold text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-4 font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</td>
                        <td class="py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->unit }}</td>
                        <td class="py-4 text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                        <td class="py-4 text-right text-gray-600 dark:text-gray-400">{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-4 text-right font-medium text-gray-900 dark:text-white">{{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <!-- Total -->
                <tr class="border-t-2 border-gray-100 dark:border-gray-700">
                    <td colspan="4" class="pt-6 text-right font-bold text-gray-900 dark:text-white text-lg">Total Amount</td>
                    <td class="pt-6 text-right font-black text-primary text-xl">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</td>
                </tr>
                @if($invoice->total_text)
                    <tr>
                        <td colspan="5" class="pt-2 text-right text-gray-500 dark:text-gray-400 italic text-sm">
                            ({{ $invoice->total_text }})
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="bg-gray-50 dark:bg-gray-900/50 p-6 border-t border-gray-100 dark:border-gray-700 text-center text-xs text-gray-500">
        <p>This is a computer-generated invoice.</p>
    </div>
</div>
