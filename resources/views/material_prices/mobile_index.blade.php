@extends('layouts.dashboard')

@section('title', __('material_prices.title'))

@section('content')
<form
    id="material-prices-bulk-form-mobile"
    action="{{ route('material-prices.bulk-destroy') }}"
    method="POST"
    class="hidden"
    @submit="
        const n = document.querySelectorAll('input[name=\'ids[]\'][form=\'material-prices-bulk-form-mobile\']:checked').length;
        if (n === 0) { $event.preventDefault(); return; }
        if (!confirm(@js(__('material_prices.confirm_bulk_delete')))) { $event.preventDefault(); }
    "
>
    @csrf
    @method('DELETE')
</form>

<div class="flex flex-col min-h-screen bg-slate-50 dark:bg-slate-900 pb-24" x-data="{ loading: false }">
    <!-- Mobile Header -->
    <div class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 py-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold dark:text-white">{{ __('material_prices.title') }}</h1>
            <div class="flex gap-2">
                <form action="{{ route('material-prices.sync') }}" method="POST" @submit="loading = true">
                    @csrf
                    <button type="submit" class="inline-flex max-w-[11rem] items-center gap-1 rounded-lg bg-purple-100 px-2 py-1.5 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <span class="material-symbols-outlined shrink-0 text-xl" :class="loading ? 'animate-spin' : ''">sync</span>
                        <span class="truncate text-[10px] font-bold leading-tight sm:text-xs">{{ __('material_prices.btn_sync') }}</span>
                    </button>
                </form>
                <a href="{{ route('material-prices.create') }}" class="inline-flex max-w-[9rem] items-center gap-1 rounded-lg bg-primary px-2 py-1.5 text-white shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined shrink-0 text-xl">add</span>
                    <span class="truncate text-[10px] font-bold leading-tight sm:text-xs">{{ __('material_prices.btn_add') }}</span>
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-3 rounded-xl text-xs font-bold border border-emerald-100 mb-2 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
            {{ session('success') }}
        </div>
        @endif

        <p class="text-[11px] text-slate-500 leading-snug mb-2">
            {{ __('material_prices.stt') }}: thứ tự trong danh sách (theo trang). {{ __('material_prices.priority') }}: thứ tự trên trang chủ.
        </p>
    </div>

    <!-- Items List -->
    <div
        class="p-4 space-y-3"
        x-data="{
            selected: 0,
            update() {
                this.selected = document.querySelectorAll('input[name=\'ids[]\'][form=\'material-prices-bulk-form-mobile\']:checked').length;
            },
            toggleAll(ev) {
                document.querySelectorAll('input.row-cb-m[form=\'material-prices-bulk-form-mobile\']').forEach(cb => { cb.checked = ev.target.checked; });
                this.update();
            }
        }"
        x-init="update()"
        @change="update()"
    >
        <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-200 dark:border-slate-700">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300">
                <input type="checkbox" class="rounded border-slate-300 text-primary" @change="toggleAll($event)">
                {{ __('material_prices.delete_selected') }}
            </label>
            <button
                type="submit"
                form="material-prices-bulk-form-mobile"
                class="inline-flex items-center gap-1 text-xs font-bold text-white bg-red-600 px-3 py-1.5 rounded-lg disabled:opacity-40"
                :disabled="selected === 0"
            >
                <span class="material-symbols-outlined text-base">delete_sweep</span>
                <span>{{ __('material_prices.delete_selected') }}</span>
                <span x-show="selected > 0" x-text="'(' + selected + ')'"></span>
            </button>
        </div>

        @forelse($prices as $price)
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group">
            <div class="flex justify-between items-start mb-2 gap-2">
                <div class="flex items-start gap-2 min-w-0 flex-1">
                    <input type="checkbox" name="ids[]" value="{{ $price->id }}" form="material-prices-bulk-form-mobile" class="row-cb-m mt-1 rounded border-slate-300 text-primary shrink-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-primary/10 flex flex-col items-center justify-center text-primary shrink-0 leading-none">
                            <span class="text-[9px] font-bold uppercase opacity-70">{{ __('material_prices.stt') }}</span>
                            <span class="font-black text-sm tabular-nums">{{ $prices->firstItem() + $loop->index }}</span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-slate-900 dark:text-white break-words">{{ $price->name }}</h3>
                            <p class="text-[10px] text-slate-400 font-semibold">{{ __('material_prices.priority') }}: {{ $price->display_order }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-1 shrink-0 items-end">
                    <a href="{{ route('material-prices.edit', $price->id) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-[11px] font-bold text-slate-500 hover:bg-primary/10 hover:text-primary">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        <span>{{ __('common.edit') }}</span>
                    </a>
                    <form action="{{ route('material-prices.destroy', $price->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Xóa vật tư này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-[11px] font-bold text-slate-500 hover:bg-red-50 hover:text-red-600">
                            <span class="material-symbols-outlined text-sm">delete</span>
                            <span>{{ __('common.delete') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="flex justify-between items-end mt-4">
                <div class="space-y-1">
                    <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Đơn giá / {{ $price->unit ?? 'đơn vị' }}</p>
                    <p class="text-xl font-black text-primary">{{ number_format($price->price, 0, ',', '.') }}đ</p>
                </div>

                <div class="flex items-center gap-1.5">
                    @if($price->is_active)
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-emerald-600 uppercase">Đang hiển thị</span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Đang ẩn</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-20 text-slate-400">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-20">inventory</span>
            <p class="font-bold">Chưa có dữ liệu</p>
            <p class="text-xs mt-1">Dùng nút Sync để đồng bộ ngay</p>
        </div>
        @endforelse
    </div>

    <div class="px-4 pb-8">
        {{ $prices->withQueryString()->links() }}
    </div>
</div>
@endsection
