@extends('layouts.dashboard')

@section('title', 'Tồn kho bãi')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="mf-admin-title text-3xl font-black">Tồn kho bãi</h1>
        <a href="{{ route('admin.stock-receipts.create') }}" class="rounded-lg bg-primary text-white font-bold px-4 py-3">Nhập kho</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="category" class="rounded-lg border-gray-300" onchange="this.form.submit()">
            <option value="">Mọi danh mục</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" @selected($category === $cat)>{{ $cat }}</option>
            @endforeach
        </select>
    </form>
    <div class="bg-white rounded-xl shadow-subtle overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Hàng</th>
                    <th class="px-4 py-2">Danh mục</th>
                    <th class="px-4 py-2">ĐVT gốc</th>
                    <th class="px-4 py-2 text-right">Tồn</th>
                    <th class="px-4 py-2 text-right">Giá vốn BQ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-t {{ (float) $product->stock_quantity < 0 ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-2 font-semibold">
                            {{ $product->name }}
                            @if((float) $product->stock_quantity < 0)
                                <span class="ml-2 rounded-full bg-red-600 text-white text-xs px-2 py-0.5">TỒN ÂM</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $product->category }}</td>
                        <td class="px-4 py-2">{{ $product->base_unit }}</td>
                        <td class="px-4 py-2 text-right font-bold">{{ number_format($product->stock_quantity, 3, ',', '.') }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($product->avg_cost, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-gray-500">Chưa có hàng trong danh mục. Tạo sản phẩm khi nhập kho hoặc qua chuyến giao.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
