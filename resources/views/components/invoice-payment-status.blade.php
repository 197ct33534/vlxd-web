@props(['invoice'])

@php
    $remaining = $invoice->outstandingAmount();
    $amount = rtrim(rtrim(number_format($remaining, 2, ',', '.'), '0'), ',').' ₫';
    $label = $invoice->code ?: '#'.$invoice->id;
@endphp

@if ($remaining <= 0)
    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
        <span aria-hidden="true" class="material-symbols-outlined text-base leading-none">check_circle</span>
        Đã thu đủ
    </span>
@else
    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1" x-data>
        <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">Còn phải thu {{ $amount }}</span>
        <button type="button" aria-haspopup="dialog" @click="$refs.paymentConfirmation.showModal()"
            class="inline-flex min-h-[44px] items-center gap-1 rounded-lg px-2 text-sm font-bold text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-900/30">
            <span aria-hidden="true" class="material-symbols-outlined text-lg">payments</span>
            Xác nhận thu đủ
        </button>

        <dialog x-ref="paymentConfirmation" aria-labelledby="confirm-payment-{{ $invoice->id }}"
            class="fixed inset-0 m-auto w-[calc(100%_-_2rem)] max-w-md rounded-2xl border border-border-light bg-container-light p-6 text-text-light shadow-2xl backdrop:bg-slate-950/75 dark:border-border-dark dark:bg-container-dark dark:text-text-dark">
            <div class="text-left">
                <span aria-hidden="true" class="material-symbols-outlined mb-3 rounded-full bg-emerald-100 p-3 text-3xl text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">payments</span>
                <h2 id="confirm-payment-{{ $invoice->id }}" class="text-xl font-bold">Xác nhận thu đủ hóa đơn {{ $label }}?</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Hệ thống sẽ ghi một phiếu thu tiền mặt cho số tiền còn thiếu.</p>
                <p class="my-4 rounded-xl bg-emerald-50 p-4 text-2xl font-black text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">{{ $amount }}</p>
            </div>
            <form action="{{ route('invoices.pay', $invoice) }}" method="POST" class="flex flex-wrap justify-end gap-2">
                @csrf
                <button type="button" autofocus @click="$refs.paymentConfirmation.close()"
                    class="min-h-[44px] rounded-lg border border-border-light px-4 font-semibold dark:border-border-dark">Hủy</button>
                <button type="submit" class="min-h-[44px] rounded-lg bg-emerald-700 px-4 font-bold text-white hover:bg-emerald-800">Xác nhận đã thu</button>
            </form>
        </dialog>
    </div>
@endif
