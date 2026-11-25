    <div x-data>
        <button type="button" aria-haspopup="dialog" @click="$refs.customerAdvance.showModal()"
            class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-primary px-3 text-sm font-semibold text-primary hover:bg-primary/10">
            <span aria-hidden="true" class="material-symbols-outlined text-lg">payments</span>
            Ứng tiền toàn bộ công trình
        </button>
        <dialog x-ref="customerAdvance" aria-labelledby="customer-advance-title"
            class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%_-_2rem)] max-w-md overflow-y-auto rounded-2xl bg-container-light p-5 text-text-light shadow-2xl backdrop:bg-slate-950/75 dark:bg-container-dark dark:text-text-dark">
            <h2 id="customer-advance-title" class="text-xl font-bold">Ứng tiền toàn bộ công trình</h2>
            <p class="mt-1 text-sm font-semibold">{{ $customer->name }}</p>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Tiền ứng được ghi một lần cho khách hàng và trừ vào công nợ tổng.</p>
            <form action="{{ route('customers.projects.advance.store', $customer) }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="hidden" name="advance_reference" value="{{ old('advance_reference', (string) \Illuminate\Support\Str::uuid()) }}">
                <label class="block text-sm font-semibold">Số tiền ứng (₫)
                    <input type="number" name="amount" min="0.01" step="0.01" inputmode="decimal" required value="{{ old('amount') }}"
                        class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">
                </label>
                <label for="customer-advance-date" class="block text-sm font-semibold">Ngày nhận tiền</label>
                <input id="customer-advance-date" type="date" name="payment_date" required x-vietnamese-date value="{{ old('payment_date', today()->toDateString()) }}"
                    class="w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">
                <label class="block text-sm font-semibold">Hình thức nhận
                    <select name="payment_method" class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">
                        <option value="cash" @selected(old('payment_method') === 'cash')>Tiền mặt</option>
                        <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Chuyển khoản</option>
                    </select>
                </label>
                <label class="block text-sm font-semibold">Ghi chú
                    <textarea name="note" rows="2" maxlength="255" class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">{{ old('note') }}</textarea>
                </label>
                <div class="flex flex-wrap justify-end gap-2">
                    <button type="button" @click="$refs.customerAdvance.close()" class="min-h-[44px] rounded-lg border border-border-light px-4 font-semibold dark:border-border-dark">Hủy</button>
                    <button type="submit" class="min-h-[44px] rounded-lg bg-primary px-4 font-bold text-white">Ghi tiền ứng</button>
                </div>
            </form>
        </dialog>
    </div>
