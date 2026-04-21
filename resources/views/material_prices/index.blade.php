@extends('layouts.dashboard')

@section('title', __('material_prices.title'))

@section('content')
<div class="flex flex-col gap-6" x-data="{ loading: false }">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">
                {{ __('material_prices.title') }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('material_prices.subtitle') }}</p>
            @unless(auth()->user()->isAdmin())
                <p class="mt-2 text-xs font-medium text-amber-700 dark:text-amber-300">{{ __('material_prices.readonly_hint') }}</p>
            @endunless
        </div>
        @if(auth()->user()->isAdmin())
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
        @endif
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3 animate-fade-in shadow-sm dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
        <span class="material-symbols-outlined">check_circle</span>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(auth()->user()->isAdmin())
    <!-- Form xóa hàng loạt (checkbox dùng form=... để không lồng form) -->
    {{-- Checkbox có form="..." nằm NGOÀI thẻ form → không dùng #form input mà phải query theo form= --}}
    <form
        id="material-prices-bulk-form"
        action="{{ route('material-prices.bulk-destroy') }}"
        method="POST"
        class="hidden"
        @submit="
            const n = document.querySelectorAll('input[name=\'ids[]\'][form=\'material-prices-bulk-form\']:checked').length;
            if (n === 0) { $event.preventDefault(); return; }
            if (!confirm(@js(__('material_prices.confirm_bulk_delete')))) { $event.preventDefault(); }
        "
    >
        @csrf
        @method('DELETE')
    </form>
    @endif

    <!-- Prices Table -->
    <div
        class="bg-container-light dark:bg-container-dark rounded-2xl shadow-subtle border border-gray-100 dark:border-gray-800 overflow-hidden"
        x-data="{
            selected: 0,
            update() {
                this.selected = document.querySelectorAll('input[name=\'ids[]\'][form=\'material-prices-bulk-form\']:checked').length;
            },
            toggleAll(ev) {
                document.querySelectorAll('input.row-cb[form=\'material-prices-bulk-form\']').forEach(cb => { cb.checked = ev.target.checked; });
                this.update();
            }
        }"
        x-init="update()"
        @change="update()"
    >
        @if(auth()->user()->isAdmin())
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/20">
            <p class="text-xs text-gray-500 dark:text-gray-400 max-w-3xl">
                <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('material_prices.stt') }}</span>:
                số thứ tự trên toàn danh sách (theo trang). Cột <span class="font-semibold">{{ __('material_prices.priority') }}</span> là thứ tự sắp xếp trên trang chủ.
            </p>
            <button
                type="submit"
                form="material-prices-bulk-form"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-sm font-bold shadow-sm disabled:opacity-40 disabled:pointer-events-none transition-colors"
                :disabled="selected === 0"
            >
                <span class="material-symbols-outlined text-lg">delete_sweep</span>
                {{ __('material_prices.delete_selected') }}
                <span x-show="selected > 0" x-text="'(' + selected + ')'" class="text-xs font-black"></span>
            </button>
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 dark:bg-gray-800/30 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <th class="px-3 py-4 font-bold text-center w-12">
                            <input type="checkbox" class="rounded border-gray-300 text-primary focus:ring-primary" @change="toggleAll($event)" title="{{ __('material_prices.delete_selected') }}" aria-label="Select all">
                        </th>
                        @endif
                        <th class="px-3 py-4 font-bold text-center w-14">{{ __('material_prices.stt') }}</th>
                        <th class="px-3 py-4 font-bold text-center w-20">{{ __('material_prices.priority') }}</th>
                        <th class="px-6 py-4 font-bold">Tên vật tư</th>
                        <th class="px-6 py-4 font-bold">Đơn vị</th>
                        <th class="px-6 py-4 font-bold text-right">Đơn giá (VNĐ)</th>
                        <th class="px-6 py-4 font-bold text-center">Trạng thái</th>
                        @if(auth()->user()->isAdmin())
                        <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    @forelse($prices as $price)
                        <tr class="hover:bg-primary/5 transition-all duration-200">
                            @if(auth()->user()->isAdmin())
                            <td class="px-3 py-4 text-center">
                                <input type="checkbox" name="ids[]" value="{{ $price->id }}" form="material-prices-bulk-form" class="row-cb rounded border-gray-300 text-primary focus:ring-primary">
                            </td>
                            @endif
                            <td class="px-3 py-4 text-center font-semibold text-gray-700 dark:text-gray-200 tabular-nums">
                                {{ $prices->firstItem() + $loop->index }}
                            </td>
                            <td class="px-3 py-4 text-center text-gray-500 tabular-nums">{{ $price->display_order }}</td>
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
                            @if(auth()->user()->isAdmin())
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-1.5">
                                    <a href="{{ route('material-prices.edit', $price->id) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-primary/10 hover:text-primary dark:text-gray-400">
                                        <span class="material-symbols-outlined text-base leading-none">edit</span>
                                        <span>{{ __('common.edit') }}</span>
                                    </a>
                                    <form action="{{ route('material-prices.destroy', $price->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Xóa vật tư này khỏi báo giá?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-red-50 hover:text-red-600 dark:text-gray-400">
                                            <span class="material-symbols-outlined text-base leading-none">delete</span>
                                            <span>{{ __('common.delete') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 8 : 6 }}" class="px-6 py-20 text-center">
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
