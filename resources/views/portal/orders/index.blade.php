@extends('layouts.mobile-customer')

@section('title', 'Đơn hàng')
@section('heading', 'Lịch sử đơn')

@section('content')
<div class="space-y-3 pb-8">
    <a href="{{ route('portal.orders.create') }}" class="mf-cta-btn mf-cta-btn--primary">+ Đặt hàng mới</a>

    @forelse($orders as $order)
        <a href="{{ route('portal.orders.show', $order) }}" class="block rounded-2xl bg-white border border-gray-200 p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-bold">{{ $order->code }}</p>
                    <p class="text-sm text-gray-500">{{ $order->project?->name ?? 'Giao ngoài CT' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $order->created_at?->format('d/m/Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-block text-xs font-bold rounded-full px-2 py-1 mb-2
                        @class([
                            'bg-amber-100 text-amber-800' => in_array($order->status, ['draft', 'pending_confirm']),
                            'bg-blue-100 text-blue-800' => $order->status === 'confirmed',
                            'bg-indigo-100 text-indigo-800' => $order->status === 'delivering',
                            'bg-green-100 text-green-800' => $order->status === 'delivered',
                            'bg-gray-100 text-gray-600' => $order->status === 'cancelled',
                        ])
                    ">{{ $order->statusLabel() }}</span>
                    <p class="mf-price text-sm">{{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                </div>
            </div>
        </a>
    @empty
        <div class="rounded-2xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
            Chưa có đơn nào.
        </div>
    @endforelse

    <div class="pt-2">{{ $orders->links() }}</div>
</div>
@endsection
