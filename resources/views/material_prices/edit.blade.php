@extends('layouts.dashboard')

@section('title', 'Sửa vật tư')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('material-prices.index') }}" class="inline-flex items-center gap-2 -ml-2 rounded-lg p-2 text-gray-400 transition-colors hover:text-primary">
            <span class="material-symbols-outlined shrink-0">arrow_back</span>
            <span class="text-sm font-semibold">{{ __('nav.back_short') }}</span>
        </a>
        <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Sửa Vật Tư</h1>
    </div>

    <div class="bg-container-light dark:bg-container-dark rounded-2xl shadow-subtle border border-gray-100 dark:border-gray-800 p-8">
        <form action="{{ route('material-prices.update', $materialPrice->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tên vật tư</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $materialPrice->name) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium"
                       placeholder="VD: Xi măng Hà Tiên Đa Dụng">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="unit" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Đơn vị tính</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $materialPrice->unit) }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium"
                           placeholder="VD: Bao, m3, Viên...">
                    @error('unit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Đơn giá (VNĐ)</label>
                    <input type="number" name="price" id="price" required step="1" value="{{ old('price', $materialPrice->price) }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-black text-primary"
                           placeholder="Nhập giá">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="display_order" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Thứ tự hiển thị</label>
                <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $materialPrice->display_order) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none font-medium"
                       placeholder="Số càng nhỏ hiển thị càng trên đầu">
                @error('display_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 py-2">
                <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', $materialPrice->is_active) ? 'checked' : '' }}
                       class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300">Hiển thị trong báo giá</label>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('material-prices.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Hủy</a>
                <button type="submit" class="px-8 py-3 rounded-xl bg-primary text-white text-sm font-black shadow-lg shadow-blue-500/20 hover:bg-blue-600 active:scale-95 transition-all">
                    Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
