@extends('layouts.dashboard')

@section('title', $order->code)

@section('content')
<div class="flex flex-col gap-6 max-w-3xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black mf-admin-title">{{ $order->code }} — {{ $order->statusLabel() }}</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-primary mf-touch">← Danh sách</a>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 text-red-800 px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl p-5 shadow-subtle space-y-2 text-sm">
        <p><strong>Khách:</strong> {{ $order->customer?->name }} ({{ $order->customer?->phone }})</p>
        <p><strong>Công trình:</strong> {{ $order->project?->name ?? '—' }}</p>
        <p><strong>Địa chỉ giao:</strong> {{ $order->delivery_address ?? '—' }}</p>
        <p><strong>Ghi chú:</strong> {{ $order->note ?? '—' }}</p>
        <p><strong>Tài xế:</strong> {{ $order->driver?->name ?? 'Chưa gán' }}</p>
        @if($order->invoice_id)
            <p><strong>Hóa đơn:</strong>
                <a class="text-primary font-bold" href="{{ route('invoices.show', $order->invoice_id) }}">
                    {{ $order->invoice?->code ?? ('#' . $order->invoice_id) }}
                </a>
            </p>
        @endif
    </div>

    @if(!$order->invoice_id && $projects->isNotEmpty())
        <form method="POST" action="{{ route('admin.orders.project.update', $order) }}"
              class="bg-white rounded-xl p-4 shadow-subtle flex flex-wrap gap-3 items-end">
            @csrf
            @method('PUT')
            <label class="flex-1 min-w-[200px]">
                <span class="text-sm font-semibold block mb-1">Gắn / đổi công trình</span>
                <select name="project_id" class="w-full rounded-lg border-gray-300 mf-touch" required>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected($order->project_id == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="rounded-lg bg-gray-800 text-white font-bold px-4 py-3 mf-touch">Lưu công trình</button>
        </form>
    @endif

    @if(in_array($order->status, ['pending_confirm', 'draft', 'confirmed']))
        <form method="POST" action="{{ route('admin.orders.items.update', $order) }}" class="bg-white rounded-xl shadow-subtle overflow-hidden">
            @csrf
            @method('PUT')
            <div class="px-4 py-3 border-b text-sm font-bold text-gray-600">Sửa giá / SL trước khi giao (khách gửi giá tham khảo)</div>
            <div class="mf-table-wrap overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-background-light"><tr>
                        <th class="px-4 py-2 text-left">Vật tư</th>
                        <th class="px-4 py-2 text-right">SL</th>
                        <th class="px-4 py-2 text-right">Giá</th>
                        <th class="px-4 py-2 text-right">Thành tiền</th>
                    </tr></thead>
                    <tbody>
                        @foreach($order->items as $i => $item)
                            <tr class="border-t">
                                <td class="px-4 py-2" data-label="Vật tư">
                                    {{ $item->product_name }} ({{ $item->unit }})
                                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}"/>
                                </td>
                                <td class="px-4 py-2 text-right" data-label="SL">
                                    <input type="number" step="0.01" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}"
                                        class="w-24 rounded border-gray-300 text-right mf-touch ml-auto"/>
                                </td>
                                <td class="px-4 py-2 text-right" data-label="Giá">
                                    <input type="number" step="1" name="items[{{ $i }}][price]" value="{{ $item->price }}"
                                        class="w-28 rounded border-gray-300 text-right mf-touch ml-auto"/>
                                </td>
                                <td class="px-4 py-2 text-right font-bold" data-label="Thành tiền">{{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t flex flex-wrap items-center justify-between gap-3">
                <p class="font-black mf-price">Tổng: {{ number_format($order->total_amount, 0, ',', '.') }} đ</p>
                <button class="rounded-lg bg-gray-800 text-white font-bold px-4 py-3 mf-touch">Lưu giá</button>
            </div>
        </form>
    @else
        <div class="bg-white rounded-xl shadow-subtle overflow-hidden">
            <div class="mf-table-wrap overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-background-light"><tr>
                        <th class="px-4 py-2 text-left">Vật tư</th>
                        <th class="px-4 py-2 text-right">SL</th>
                        <th class="px-4 py-2 text-right">Giá</th>
                        <th class="px-4 py-2 text-right">Thành tiền</th>
                    </tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-t">
                                <td class="px-4 py-2" data-label="Vật tư">{{ $item->product_name }} ({{ $item->unit }})</td>
                                <td class="px-4 py-2 text-right" data-label="SL">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right" data-label="Giá">{{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right font-bold" data-label="Thành tiền">{{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t text-right font-black mf-price">
                Tổng: {{ number_format($order->total_amount, 0, ',', '.') }} đ
            </div>
        </div>
    @endif

    <div class="flex flex-wrap gap-3">
        @if(in_array($order->status, ['pending_confirm', 'draft']))
            <form method="POST" action="{{ route('admin.orders.confirm', $order) }}">
                @csrf
                <button class="rounded-lg bg-primary text-white font-bold px-4 py-3 mf-touch">Xác nhận đơn</button>
            </form>
        @endif

        @if($order->canExportInvoice())
            <form method="POST" action="{{ route('admin.orders.export-invoice', $order) }}"
                  onsubmit="return confirm('Xuất hóa đơn vào công trình và cập nhật công nợ?')">
                @csrf
                <button class="rounded-lg bg-emerald-600 text-white font-bold px-4 py-3 mf-touch">Xuất hóa đơn</button>
            </form>
        @elseif($order->invoice_id)
            <a href="{{ route('invoices.show', $order->invoice_id) }}"
               class="rounded-lg bg-emerald-50 text-emerald-800 font-bold px-4 py-3 mf-touch inline-flex items-center">
                Xem hóa đơn {{ $order->invoice?->code ?? ('#' . $order->invoice_id) }}
            </a>
        @elseif(in_array($order->status, ['confirmed', 'delivering', 'delivered']) && !$order->project_id)
            <p class="text-sm text-amber-700 bg-amber-50 rounded-xl px-4 py-3 w-full">
                Cần gắn công trình trước khi xuất hóa đơn.
            </p>
        @endif

        @if(!in_array($order->status, ['delivered', 'cancelled']))
            <form method="POST" action="{{ route('admin.orders.cancel', $order) }}" onsubmit="return confirm('Hủy đơn?')">
                @csrf
                <button class="rounded-lg bg-red-600 text-white font-bold px-4 py-3 mf-touch">Hủy</button>
            </form>
        @endif
    </div>

    @if($drivers->isNotEmpty() && in_array($order->status, ['confirmed', 'delivering']))
        <form method="POST" action="{{ route('admin.orders.assign-driver', $order) }}" class="bg-white rounded-xl p-4 shadow-subtle flex flex-wrap gap-3 items-end">
            @csrf
            <label class="flex-1 min-w-[200px]">
                <span class="text-sm font-semibold block mb-1">Gán tài xế</span>
                <select name="driver_id" class="w-full rounded-lg border-gray-300 mf-touch" required>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" @selected($order->driver_id == $driver->id)>{{ $driver->name }}</option>
                    @endforeach
                </select>
            </label>
            <button class="rounded-lg bg-indigo-600 text-white font-bold px-4 py-3 mf-touch">Lưu tài xế</button>
        </form>
    @elseif($drivers->isEmpty() && in_array($order->status, ['confirmed', 'delivering']))
        <p class="text-sm text-amber-700 bg-amber-50 rounded-xl px-4 py-3">
            Chưa có user <code>role=driver</code>. Chạy seeder demo hoặc tạo user tài xế rồi gán đơn.
        </p>
    @endif
</div>
@endsection
