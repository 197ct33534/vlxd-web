@php
    $balance = $customer->accountBalance();
    $adjustment = round((float) $customer->account_project_paid - (float) $customer->account_receipts
        + (float) $customer->account_advanced - (float) $customer->account_advance_receipts, 2);
    $format = fn ($amount) => rtrim(rtrim(number_format($amount, 2, ',', '.'), '0'), ',') . ' ₫';
@endphp
<section aria-labelledby="customer-balance-title" class="rounded-xl border border-border-light bg-container-light p-4 dark:border-border-dark dark:bg-container-dark">
    <h2 id="customer-balance-title" class="mb-3 text-lg font-bold">Đối chiếu công nợ khách hàng</h2>
    <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
        <div>
            <dt class="text-gray-600 dark:text-gray-300">Tổng hóa đơn các công trình</dt>
            <dd class="font-semibold tabular-nums">{{ $format((float) $customer->account_invoiced) }}</dd>
        </div>
        <div>
            <dt class="text-gray-600 dark:text-gray-300">Khách đã thanh toán</dt>
            <dd class="font-semibold tabular-nums">{{ $format((float) $customer->account_receipts + (float) $customer->account_advance_receipts) }}</dd>
            @if ($customer->account_advance_receipts)
                <dd class="text-xs text-gray-600 dark:text-gray-300">Trong đó ứng chung {{ $format((float) $customer->account_advance_receipts) }}</dd>
            @endif
            @if ($adjustment !== 0.0)
                <dd class="text-xs text-gray-600 dark:text-gray-300">Điều chỉnh công nợ {{ $format($adjustment) }}</dd>
            @endif
        </div>
        <div>
            <dt class="text-gray-600 dark:text-gray-300">{{ $balance < 0 ? 'Số dư trả trước' : 'Công nợ còn lại' }}</dt>
            <dd class="font-bold tabular-nums {{ $balance > 0 ? 'text-red-600' : 'text-emerald-700 dark:text-emerald-300' }}">{{ $format(abs($balance)) }}</dd>
        </div>
    </dl>
    @include('invoices.partials.debt-adjustment', ['forCustomer' => true])
</section>
