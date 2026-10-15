@extends('layouts.mobile-customer')

@section('title', 'Trang chủ')
@section('heading', $customer->name)

@section('content')
<div class="space-y-4 pb-8">
    <div class="rounded-2xl bg-primary text-white p-5">
        <p class="text-sm opacity-90">Tổng công nợ các công trình</p>
        <p class="mf-price text-3xl mt-1">{{ number_format($totalDebt, 0, ',', '.') }} đ</p>
        <a href="{{ route('portal.orders.create') }}" class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-white text-primary font-bold px-4 py-3 mf-touch">
            <span class="material-symbols-outlined">add_shopping_cart</span>
            Đặt hàng nhanh
        </a>
    </div>

    <section>
        <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Công trình</h2>
        <div class="space-y-3">
            @forelse($projects as $project)
                <div class="rounded-2xl bg-white border border-gray-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-bold truncate">{{ $project->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $project->address }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-semibold rounded-full px-2 py-1 bg-gray-100">
                            {{ str_replace('_', ' ', $project->status) }}
                        </span>
                    </div>
                    <div class="mt-3 flex items-end justify-between">
                        <div>
                            <p class="text-xs text-gray-500">Còn nợ</p>
                            <p class="mf-price text-primary">{{ number_format($project->total_debt, 0, ',', '.') }} đ</p>
                        </div>
                        <a href="{{ route('portal.orders.create', ['project_id' => $project->id]) }}" class="text-sm font-bold text-primary mf-touch inline-flex items-center">
                            Đặt cho CT này
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-white border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                    Chưa có công trình. Gọi cửa hàng để mở công trình mới.
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide">Đơn gần đây</h2>
            <a href="{{ route('portal.orders.index') }}" class="text-sm font-semibold text-primary">Xem tất cả</a>
        </div>
        <div class="space-y-2">
            @forelse($recentOrders as $order)
                <a href="{{ route('portal.orders.show', $order) }}" class="block rounded-xl bg-white border border-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="font-semibold">{{ $order->code }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-xs font-bold rounded-full px-2 py-1
                            @class([
                                'bg-amber-100 text-amber-800' => in_array($order->status, ['draft', 'pending_confirm']),
                                'bg-blue-100 text-blue-800' => $order->status === 'confirmed',
                                'bg-indigo-100 text-indigo-800' => $order->status === 'delivering',
                                'bg-green-100 text-green-800' => $order->status === 'delivered',
                                'bg-gray-100 text-gray-600' => $order->status === 'cancelled',
                            ])
                        ">{{ $order->statusLabel() }}</span>
                    </div>
                </a>
            @empty
                <p class="text-sm text-gray-500">Chưa có đơn. Bấm Đặt hàng nhanh để bắt đầu.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
