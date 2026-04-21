{{-- Nội dung chi tiết hóa đơn (dùng trong trang show + modal xem nhanh) --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
    {{-- Phần đầu --}}
    <div class="border-b border-gray-200 bg-gradient-to-br from-primary/10 via-white to-slate-50 px-5 py-5 dark:border-gray-700 dark:from-primary/20 dark:via-gray-900 dark:to-gray-900 sm:px-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">{{ __('invoices.detail.heading') }}</p>
                <h2 class="mt-1 text-2xl font-black tracking-tight text-gray-900 dark:text-white">
                    {{ $invoice->code ?? 'HD-'.str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('invoices.detail.code_label') }}
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $invoice->code ?? '#'.$invoice->id }}</span>
                </p>
            </div>
            <div class="rounded-lg border border-gray-200/80 bg-white/80 px-4 py-3 text-right dark:border-gray-600 dark:bg-gray-800/80">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('invoices.detail.date_label') }}</p>
                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                    {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : '—' }}
                </p>
            </div>
        </div>

        <div class="mt-6 grid gap-6 sm:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-white/60 p-4 dark:border-gray-700 dark:bg-gray-800/40">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('invoices.detail.customer_section') }}</p>
                <p class="mt-2 text-base font-bold text-gray-900 dark:text-white">{{ $invoice->project->customer->name ?? '—' }}</p>
                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ __('invoices.detail.project_name') }}: {{ $invoice->project->name ?? '—' }}</p>
                @if($invoice->project->address)
                    <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $invoice->project->address }}</p>
                @endif
            </div>
            @if($invoice->note)
                <div class="rounded-lg border border-amber-100 bg-amber-50/80 p-4 dark:border-amber-900/40 dark:bg-amber-950/30">
                    <p class="text-xs font-bold uppercase tracking-wide text-amber-800 dark:text-amber-200">{{ __('invoices.table.note') }}</p>
                    <p class="mt-2 text-sm italic leading-relaxed text-amber-950 dark:text-amber-100">{{ $invoice->note }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Bảng hàng --}}
    <div class="px-4 py-5 sm:px-6">
        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('invoices.detail.items_heading') }}</p>
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-slate-100 text-xs font-bold uppercase tracking-wide text-slate-600 dark:border-gray-600 dark:bg-slate-800 dark:text-slate-300">
                        <th class="px-4 py-3">{{ __('invoices.table.product_name') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('common.unit') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('common.quantity') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('common.price') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('invoices.table.line_total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($invoice->items as $item)
                        <tr class="bg-white hover:bg-slate-50/80 dark:bg-gray-900 dark:hover:bg-gray-800/80">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $item->unit }}</td>
                            <td class="px-4 py-3 text-center tabular-nums text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-700 dark:text-gray-300">{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-primary/30 bg-primary/5 dark:border-primary/40 dark:bg-primary/10">
                        <td colspan="4" class="px-4 py-4 text-right text-base font-bold text-gray-800 dark:text-gray-100">
                            {{ __('invoices.detail.total_payment') }}
                        </td>
                        <td class="px-4 py-4 text-right text-xl font-black text-primary">
                            {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    @if($invoice->total_text)
                        <tr>
                            <td colspan="5" class="px-4 pb-4 text-right text-sm italic text-gray-500 dark:text-gray-400">
                                ({{ __('invoices.detail.bang_chu') }}: {{ $invoice->total_text }})
                            </td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

    <div class="border-t border-gray-100 bg-slate-50 px-4 py-3 text-center text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-800/50 dark:text-gray-400">
        {{ __('invoices.detail.footer_note') }}
    </div>
</div>
