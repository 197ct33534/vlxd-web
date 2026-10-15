@extends('layouts.dashboard')

@section('title', 'Đơn đặt hàng')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="mf-admin-title text-3xl font-black tracking-tight">Đơn đặt hàng (Mobile)</h1>
        <div class="flex flex-wrap gap-2">
            @foreach([null => 'Tất cả', 'pending_confirm' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'delivering' => 'Đang giao', 'delivered' => 'Đã giao', 'need_invoice' => 'Chưa xuất HĐ'] as $key => $label)
                <a href="{{ route('admin.orders.index', array_filter(['status' => $key])) }}"
                   class="rounded-lg px-3 py-2 text-sm font-bold mf-touch {{ $status === $key ? 'bg-primary text-white' : 'bg-white border' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="mf-table-wrap bg-white rounded-xl shadow-subtle overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-background-light text-xs uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Mã</th>
                    <th class="px-4 py-3 text-left">Khách</th>
                    <th class="px-4 py-3 text-left">Công trình</th>
                    <th class="px-4 py-3 text-left">Trạng thái</th>
                    <th class="px-4 py-3 text-right">Tổng</th>
                    <th class="px-4 py-3 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t">
                        <td class="px-4 py-3" data-label="Mã">{{ $order->code }}</td>
                        <td class="px-4 py-3" data-label="Khách">{{ $order->customer?->name }}</td>
                        <td class="px-4 py-3" data-label="Công trình">{{ $order->project?->name ?? '—' }}</td>
                        <td class="px-4 py-3" data-label="Trạng thái">
                            {{ $order->statusLabel() }}
                            @if($order->canExportInvoice())
                                <span class="ml-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded px-1.5 py-0.5">Chưa HĐ</span>
                            @elseif($order->invoice_id)
                                <span class="ml-1 text-[10px] font-bold text-gray-500">Đã HĐ</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right mf-price" data-label="Tổng">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right" data-label="Thao tác">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary font-bold">Chi tiết</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Chưa có đơn.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $orders->withQueryString()->links() }}
</div>
@endsection
