<section id="customer-payment-history" aria-labelledby="customer-payment-title"
    class="scroll-mt-4 overflow-hidden rounded-xl border border-border-light bg-container-light dark:border-border-dark dark:bg-container-dark">
    <div class="border-b border-border-light px-4 py-3 dark:border-border-dark">
        <h2 id="customer-payment-title" class="text-lg font-bold">Lịch sử thanh toán</h2>
        <p class="text-sm text-gray-600 dark:text-gray-300">Toàn bộ công trình của {{ $customer->name }} · {{ $payments->total() }} giao dịch</p>
    </div>

    <ul class="divide-y divide-border-light dark:divide-border-dark">
        @forelse ($payments as $payment)
            @php
                $customerAdvance = $payment->is_customer_advance || str_starts_with($payment->reference_code ?? '', 'customer-advance:');
                $label = $payment->isDebtAdjustment()
                    ? ($payment->is_customer_advance ? 'Điều chỉnh công nợ tổng' : 'Điều chỉnh công nợ')
                    : ($customerAdvance ? 'Ứng tiền toàn bộ công trình' : 'Thanh toán');
                $amount = rtrim(rtrim(number_format($payment->amount, 2, ',', '.'), '0'), ',');
            @endphp
            <li class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2 px-4 py-3">
                <div class="min-w-[min(100%,14rem)] flex-1 [overflow-wrap:anywhere]">
                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                        {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') . ' · ' : '' }}{{ $label }}
                    </p>
                    @if ($payment->project)
                        <a href="{{ route('projects.invoices.index', $payment->project) }}" class="mt-1 inline-block text-sm font-semibold text-primary underline-offset-2 hover:underline">
                            {{ $payment->project->displayName() }}
                        </a>
                    @else
                        <p class="mt-1 text-sm font-semibold">{{ $payment->isDebtAdjustment() ? 'Toàn bộ công trình của khách hàng' : 'Khoản ứng chung của khách hàng' }}</p>
                    @endif
                    @if (filled($payment->note))
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $payment->note }}</p>
                    @endif
                    @if ($customerAdvance && !$payment->isDebtAdjustment())
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" title="{{ $payment->reference_code }}">Mã đợt ứng {{ substr($payment->reference_code, -8) }}</p>
                    @endif
                </div>
                <p class="max-w-full text-sm font-bold tabular-nums [overflow-wrap:anywhere] {{ $payment->isDebtAdjustment() ? 'text-amber-700 dark:text-amber-300' : 'text-emerald-700 dark:text-emerald-300' }}">{{ $amount }} ₫</p>
            </li>
        @empty
            <li class="px-4 py-5 text-sm text-gray-600 dark:text-gray-300">Chưa có giao dịch thanh toán cho khách hàng này.</li>
        @endforelse
    </ul>

    {{ $payments->onEachSide(1)->links('components.pagination-links', ['navigationLabel' => 'Phân trang lịch sử thanh toán']) }}
</section>
