@php
    $forCustomer = $forCustomer ?? false;
    $subject = $forCustomer ? $customer : $project;
    $balance = $forCustomer ? $customer->accountBalance() : (float) $project->total_debt;
    $title = $forCustomer ? 'Cập nhật công nợ tổng' : 'Cập nhật công nợ';
    $action = $forCustomer ? route('customers.projects.debt-adjustment.store', $customer) : route('projects.debt-adjustments.store', $project);
    $reasonLength = $forCustomer ? 160 : 255;
@endphp
<div x-data class="mt-4">
    <button type="button" aria-haspopup="dialog" @click="$refs.adjustDebt.showModal()"
        class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-primary px-4 text-sm font-bold text-primary hover:bg-primary/10">
        <span aria-hidden="true" class="material-symbols-outlined text-lg">balance</span>
        {{ $title }}
    </button>
    <dialog x-ref="adjustDebt" aria-labelledby="adjust-debt-title"
        class="fixed inset-0 m-auto w-[calc(100%_-_2rem)] max-w-md rounded-2xl bg-container-light p-6 text-left text-text-light shadow-2xl backdrop:bg-slate-950/75 dark:bg-container-dark dark:text-text-dark">
        <h2 id="adjust-debt-title" class="text-xl font-bold">{{ $title }}</h2>
        <p class="mt-2 text-sm">{{ $subject->name }} · {{ $balance < 0 ? 'Số dư trả trước' : 'Đang nợ' }} <strong>{{ number_format(abs($balance), 2, ',', '.') }} ₫</strong></p>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Chênh lệch sẽ được ghi vào lịch sử thanh toán của {{ $forCustomer ? 'khách hàng' : 'công trình' }}.</p>
        <form action="{{ $action }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="expected_debt" value="{{ $balance }}">
            <label class="block text-sm font-semibold">
                Công nợ thực tế (₫)
                <input type="number" name="debt_amount" min="0" step="0.01" inputmode="decimal" required
                    value="{{ old('debt_amount', $balance < 0 ? '' : $balance) }}"
                    class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">
            </label>
            <label class="block text-sm font-semibold">
                Lý do điều chỉnh
                <textarea name="adjustment_reason" rows="2" maxlength="{{ $reasonLength }}" required
                    class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">{{ old('adjustment_reason') }}</textarea>
            </label>
            <div class="flex flex-wrap justify-end gap-2">
                <button type="button" autofocus @click="$refs.adjustDebt.close()" class="min-h-[44px] rounded-lg border border-border-light px-4 font-semibold dark:border-border-dark">Hủy</button>
                <button type="submit" class="min-h-[44px] rounded-lg bg-primary px-4 font-bold text-white">Ghi điều chỉnh</button>
            </div>
        </form>
    </dialog>
</div>
