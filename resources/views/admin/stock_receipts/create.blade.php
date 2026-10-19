@extends('layouts.dashboard')

@section('title', 'Nhập kho')

@section('content')
<div class="flex flex-col gap-6 max-w-4xl">
    <h1 class="mf-admin-title text-3xl font-black">Nhập kho từ nhà cung cấp</h1>
    <form method="POST" action="{{ route('admin.stock-receipts.store') }}" class="bg-white rounded-xl p-4 shadow-subtle space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-3">
            <label>
                <span class="text-sm font-semibold">Nhà cung cấp có sẵn</span>
                <select name="supplier_id" class="w-full rounded-lg border-gray-300">
                    <option value="">—</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="text-sm font-semibold">Hoặc tên NCC mới</span>
                <input name="supplier_name" class="w-full rounded-lg border-gray-300" value="{{ old('supplier_name') }}">
            </label>
            <label>
                <span class="text-sm font-semibold">Ngày nhập</span>
                <input type="date" name="receipt_date" value="{{ old('receipt_date', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
            </label>
            <label>
                <span class="text-sm font-semibold">Ghi chú</span>
                <input name="note" class="w-full rounded-lg border-gray-300" value="{{ old('note') }}">
            </label>
        </div>
        <div class="space-y-2" x-data="{ rows: 1 }">
            <template x-for="i in rows" :key="i">
                <div class="grid sm:grid-cols-3 gap-2 border rounded-lg p-2">
                    <select :name="'items['+(i-1)+'][product_id]'" class="rounded-lg border-gray-300">
                        <option value="">Hàng mới</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <input :name="'items['+(i-1)+'][product_name]'" placeholder="Tên hàng mới" class="rounded-lg border-gray-300">
                    <input :name="'items['+(i-1)+'][base_unit]'" placeholder="ĐVT gốc" class="rounded-lg border-gray-300">
                    <input :name="'items['+(i-1)+'][category]'" placeholder="Danh mục" class="rounded-lg border-gray-300">
                    <input type="number" step="0.001" min="0" :name="'items['+(i-1)+'][quantity]'" placeholder="Số lượng" class="rounded-lg border-gray-300">
                    <input type="number" step="1" min="0" :name="'items['+(i-1)+'][unit_price]'" placeholder="Giá mua" class="rounded-lg border-gray-300">
                </div>
            </template>
            <button type="button" class="text-sm font-bold text-primary" @click="rows++">+ Thêm dòng</button>
        </div>
        <button class="rounded-lg bg-primary text-white font-bold px-4 py-3">Lưu phiếu nhập</button>
    </form>
</div>
@endsection
