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
            <form method="POST" action="{{ route('admin.orders.confirm', $order) }}" class="flex flex-wrap items-center gap-3">
                @csrf
                @if(!empty($credit['is_exceeded']))
                    <label class="flex items-center gap-2 text-sm font-semibold text-amber-800 bg-amber-50 rounded-lg px-3 py-2">
                        <input type="checkbox" name="credit_confirmed" value="1">
                        Tôi là Admin xác nhận tiếp tục cấp hàng. {{ $credit['reason'] }}
                    </label>
                @endif
                <button class="rounded-lg bg-primary text-white font-bold px-4 py-3 mf-touch">Xác nhận đơn</button>
            </form>
        @endif

        @if($order->canExportInvoice())
            <form method="POST" action="{{ route('admin.orders.export-invoice', $order) }}"
                  class="flex flex-wrap items-end gap-2"
                  onsubmit="return confirm('Xuất hóa đơn vào công trình và cập nhật công nợ?')">
                @csrf
                <label>
                    <span class="text-xs font-semibold block mb-1">VAT %</span>
                    <input type="number" name="vat_rate" value="0" min="0" max="100" step="0.01"
                           class="w-24 rounded-lg border-gray-300 mf-touch">
                </label>
                <button class="rounded-lg bg-emerald-600 text-white font-bold px-4 py-3 mf-touch">Xuất hóa đơn</button>
            </form>
        @elseif($order->invoice_id)
            <a href="{{ route('invoices.show', $order->invoice_id) }}"
               class="rounded-lg bg-emerald-50 text-emerald-800 font-bold px-4 py-3 mf-touch inline-flex items-center">
                Xem hóa đơn {{ $order->invoice?->code ?? ('#' . $order->invoice_id) }}
            </a>
        @elseif(in_array($order->status, ['confirmed', 'delivering', 'partially_delivered', 'delivered']) && !$order->project_id)
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

    <div class="bg-white rounded-xl shadow-subtle overflow-hidden">
        <div class="px-4 py-3 border-b font-bold">Chuyến giao hàng</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-2">Mã</th>
                        <th class="px-4 py-2">Ngày</th>
                        <th class="px-4 py-2">Tài xế</th>
                        <th class="px-4 py-2">Biển số</th>
                        <th class="px-4 py-2">Trạng thái</th>
                        <th class="px-4 py-2 text-right">Phí</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->deliveryNotes as $note)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-bold">{{ $note->code }}</td>
                            <td class="px-4 py-2">{{ $note->delivery_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $note->driver?->name ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $note->license_plate ?? '—' }}</td>
                            <td class="px-4 py-2">{{ $note->statusLabel() }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($note->shipping_fee, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-3 text-gray-500">Chưa có chuyến.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($order->project_id && in_array($order->status, ['confirmed', 'delivering', 'partially_delivered']))
        @php
            $shipped = $order->deliveryNotes
                ->where('status', '!=', 'cancelled')
                ->flatMap->items
                ->groupBy('order_item_id')
                ->map(fn ($rows) => (float) $rows->sum('quantity'));
        @endphp
        <form method="POST" action="{{ route('admin.orders.delivery-notes.store', $order) }}" class="bg-white rounded-xl p-4 shadow-subtle space-y-3">
            @csrf
            <p class="font-bold">Tạo chuyến mới</p>
            <div class="flex flex-wrap gap-3">
                <label class="min-w-[180px] flex-1">
                    <span class="text-sm font-semibold block mb-1">Tài xế</span>
                    <select name="driver_id" class="w-full rounded-lg border-gray-300">
                        <option value="">—</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="min-w-[140px]">
                    <span class="text-sm font-semibold block mb-1">Biển số</span>
                    <input name="license_plate" class="w-full rounded-lg border-gray-300" maxlength="20">
                </label>
                <label class="min-w-[140px]">
                    <span class="text-sm font-semibold block mb-1">Phí vận chuyển / hạ hàng</span>
                    <input type="number" name="shipping_fee" value="0" min="0" step="1000" class="w-full rounded-lg border-gray-300">
                </label>
            </div>
            <label class="flex items-center gap-2 text-sm font-semibold">
                <input type="checkbox" name="driver_collected_cash" value="1">
                Tài xế đã thu tiền phí / cước
            </label>
            <label class="flex items-center gap-2 text-sm font-semibold">
                <input type="checkbox" name="is_direct_delivery" value="1">
                Giao thẳng từ nhà máy (không trừ kho bãi)
            </label>
            <div class="space-y-2">
                @foreach($order->items as $i => $item)
                    @php $remain = max(0, (float) $item->quantity - (float) ($shipped[$item->id] ?? 0)); @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 items-end border rounded-lg p-3"
                         x-data="{ qty: {{ $remain }}, factor: 1, converted: {{ $remain }}, overridden: false }">
                        <input type="hidden" name="items[{{ $i }}][order_item_id]" value="{{ $item->id }}">
                        <input type="hidden" name="items[{{ $i }}][trade_unit]" value="{{ $item->unit }}">
                        <label class="sm:col-span-2">
                            <span class="text-xs font-semibold">Hàng trong danh mục (để trừ kho bãi)</span>
                            <select name="items[{{ $i }}][product_id]" class="w-full rounded-lg border-gray-300">
                                <option value="">— Không trừ kho —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->base_unit }})</option>
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="items[{{ $i }}][is_overridden]" :value="overridden ? 1 : 0">
                        <p class="text-sm font-semibold sm:col-span-4">{{ $item->product_name }} — còn {{ number_format($remain, 3, ',', '.') }} {{ $item->unit }}</p>
                        <label>
                            <span class="text-xs font-semibold">Số lượng bán ({{ $item->unit }})</span>
                            <input type="number" step="0.001" min="0" name="items[{{ $i }}][quantity]" x-model.number="qty"
                                   @input="if (!overridden) converted = +(qty * factor).toFixed(3)"
                                   class="w-full rounded-lg border-gray-300">
                        </label>
                        <label>
                            <span class="text-xs font-semibold">Quy đổi (kg / m³)</span>
                            <input type="number" step="0.001" min="0" name="items[{{ $i }}][converted_quantity]" x-model.number="converted"
                                   @input="overridden = true"
                                   class="w-full rounded-lg border-gray-300">
                        </label>
                    </div>
                @endforeach
            </div>
            <button class="rounded-lg bg-primary text-white font-bold px-4 py-3">Tạo chuyến</button>
        </form>
    @endif

    @if($drivers->isNotEmpty() && in_array($order->status, ['confirmed', 'delivering', 'partially_delivered']))
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
