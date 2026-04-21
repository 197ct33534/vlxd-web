@extends('layouts.dashboard')

@section('title', 'Quản lý báo giá vật tư')

@section('content')
<div class="flex flex-col gap-6" x-data="{ loading: false }">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">
                Bảng Báo Giá Vật Tư
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Quản lý danh mục và giá vật tư hiển thị trên Landing Page</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('material-prices.sync') }}" method="POST" @submit="loading = true">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-lg transition-all text-sm font-bold shadow-lg shadow-purple-500/20 active:scale-95">
                    <span class="material-symbols-outlined text-lg" :class="loading ? 'animate-spin' : ''">sync</span>
                    Đồng bộ từ hóa đơn
                </button>
            </form>
            <a href="{{ route('material-prices.create') }}" class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2.5 rounded-lg transition-all text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Thêm vật tư mới
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in shadow-sm dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Prices Table -->
    <div class="bg-container-light dark:bg-container-dark rounded-2xl shadow-subtle border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-4 font-bold text-center w-16">Thứ tự</th>
                        <th class="px-6 py-4 font-bold">Tên vật tư</th>
                        <th class="px-6 py-4 font-bold">Đơn vị</th>
                        <th class="px-6 py-4 font-bold text-right">Đơn giá (VNĐ)</th>
                        <th class="px-6 py-4 font-bold text-center">Trạng thái</th>
                        <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    @forelse($prices as $price)
                        <tr class="hover:bg-primary/5 transition-all duration-200">
                            <td class="px-6 py-4 text-center font-medium text-gray-400">{{ $price->display_order }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $price->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $price->unit }}</td>
                            <td class="px-6 py-4 text-right font-black text-primary">
                                {{ number_format($price->price, 0, ',', '.') }}đ
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($price->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Hiển thị</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 border border-gray-200 dark:border-gray-700">Ẩn</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('material-prices.edit', $price->id) }}" class="p-2 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all" title="Sửa">
                                        <span class="material-symbols-outlined">edit</span>
                                    </a>
                                    <form action="{{ route('material-prices.destroy', $price->id) }}" method="POST" class="inline" onsubmit="return confirm('Xóa vật tư này khỏi báo giá?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Xóa">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="material-symbols-outlined text-5xl text-gray-200">inventory_2</span>
                                    <p class="text-gray-400 font-medium">Chưa có dữ liệu báo giá.</p>
                                    <p class="text-xs text-gray-300">Nhấn nút "Đồng bộ" để lấy dữ liệu từ hóa đơn cũ.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-800">
            {{ $prices->withQueryString()->links() }}
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
</style>
@endsection
