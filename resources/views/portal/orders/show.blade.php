@extends('layouts.mobile-customer')

@section('title', $order->code)
@section('heading', $order->code)

@section('content')
<div
    class="space-y-4 pb-8"
    x-data="orderStatusWatcher(@js([
        'status' => $order->status,
        'statusLabel' => $order->statusLabel(),
        'invoiceId' => $order->invoice_id,
        'pollUrl' => route('portal.orders.status', $order),
        'terminal' => in_array($order->status, ['delivered', 'cancelled'], true),
    ]))"
>
    <div class="rounded-2xl bg-white border border-gray-200 p-4">
        <div class="flex items-center justify-between gap-2">
            <div>
                <p class="text-sm text-gray-500">Trạng thái</p>
                <p class="text-xl font-black mt-1" x-text="statusLabel">{{ $order->statusLabel() }}</p>
            </div>
            <span x-show="polling && !terminal" class="text-xs text-gray-400 animate-pulse">Đang cập nhật…</span>
        </div>
        <div class="mt-4 grid grid-cols-4 gap-1 text-[10px] font-bold text-center">
            <template x-for="step in steps" :key="step.key">
                <div class="rounded-lg py-2"
                     :class="isActive(step.key) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500'"
                     x-text="step.label"></div>
            </template>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 p-4 space-y-2 text-sm">
        <p><span class="text-gray-500">Công trình:</span> {{ $order->project?->name ?? '—' }}</p>
        <p><span class="text-gray-500">Giao đến:</span> {{ $order->delivery_address ?? '—' }}</p>
        <p><span class="text-gray-500">Tạo lúc:</span> {{ $order->created_at?->format('d/m/Y H:i') }}</p>
        <p x-show="invoiceId" x-cloak class="rounded-lg bg-emerald-50 text-emerald-800 px-3 py-2 font-semibold">
            Shop đã xuất hóa đơn (cộng công nợ công trình).
        </p>
        @if($order->note)
            <p><span class="text-gray-500">Ghi chú:</span> {{ $order->note }}</p>
        @endif
    </div>

    <div class="rounded-2xl bg-white border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b font-bold">Vật tư</div>
        <ul class="divide-y">
            @foreach($order->items as $item)
                <li class="px-4 py-3 flex justify-between gap-3 text-sm">
                    <div>
                        <p class="font-semibold">{{ $item->product_name }}</p>
                        <p class="text-gray-500">{{ number_format($item->quantity, 2, ',', '.') }} {{ $item->unit }} × {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                    <p class="mf-price">{{ number_format($item->amount, 0, ',', '.') }}</p>
                </li>
            @endforeach
        </ul>
        <div class="px-4 py-3 border-t flex justify-between font-bold">
            <span>Tổng</span>
            <span class="mf-price text-primary">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
        </div>
    </div>

    <a href="{{ route('portal.orders.create', ['reorder' => $order->id, 'project_id' => $order->project_id]) }}"
       class="mf-cta-btn mf-cta-btn--secondary">Đặt lại đơn này</a>
</div>
@endsection

@push('scripts')
<script>
function orderStatusWatcher(initial) {
    return {
        status: initial.status,
        statusLabel: initial.statusLabel,
        invoiceId: initial.invoiceId,
        pollUrl: initial.pollUrl,
        terminal: initial.terminal,
        polling: false,
        timer: null,
        steps: [
            { key: 'pending_confirm', label: 'Chờ' },
            { key: 'confirmed', label: 'Xác nhận' },
            { key: 'delivering', label: 'Đang giao' },
            { key: 'delivered', label: 'Xong' },
        ],
        isActive(key) {
            if (this.status === 'draft' && key === 'pending_confirm') return true;
            if (this.status === 'cancelled') return false;
            const order = ['pending_confirm', 'confirmed', 'delivering', 'delivered'];
            return order.indexOf(this.status) >= order.indexOf(key);
        },
        init() {
            if (this.terminal) return;
            this.timer = setInterval(() => this.poll(), 8000);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden && !this.terminal) this.poll();
            });
        },
        async poll() {
            if (this.terminal) {
                clearInterval(this.timer);
                return;
            }
            this.polling = true;
            try {
                const res = await fetch(this.pollUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.status && data.status !== this.status) {
                    this.status = data.status;
                    this.statusLabel = data.status_label;
                    this.invoiceId = data.invoice_id;
                    if (['delivered', 'cancelled'].includes(data.status)) {
                        this.terminal = true;
                        clearInterval(this.timer);
                    }
                } else if (data.invoice_id && !this.invoiceId) {
                    this.invoiceId = data.invoice_id;
                }
            } catch (e) {
            } finally {
                this.polling = false;
            }
        },
        destroy() {
            if (this.timer) clearInterval(this.timer);
        },
    };
}
</script>
@endpush
