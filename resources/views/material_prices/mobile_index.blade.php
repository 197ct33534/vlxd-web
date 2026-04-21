@extends('layouts.dashboard')

@section('title', 'Quản lý báo giá')

@section('content')
<div class="flex flex-col min-h-screen bg-slate-50 dark:bg-slate-900 pb-20" x-data="{ loading: false }">
    <!-- Mobile Header -->
    <div class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 py-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold dark:text-white">Báo giá vật tư</h1>
            <div class="flex gap-2">
                <form action="{{ route('material-prices.sync') }}" method="POST" @submit="loading = true">
                    @csrf
                    <button type="submit" class="p-2 bg-purple-100 text-purple-600 rounded-lg dark:bg-purple-900/30 dark:text-purple-400">
                        <span class="material-symbols-outlined text-xl" :class="loading ? 'animate-spin' : ''">sync</span>
                    </button>
                </form>
                <a href="{{ route('material-prices.create') }}" class="p-2 bg-primary text-white rounded-lg shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-xl">add</span>
                </a>
            </div>
        </div>
        
        @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-3 rounded-xl text-xs font-bold border border-emerald-100 mb-2 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-400">
            {{ session('success') }}
        </div>
        @endif
    </div>

    <!-- Items List -->
    <div class="p-4 space-y-3">
        @forelse($prices as $price)
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm relative overflow-hidden group">
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                        #{{ $price->display_order }}
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white">{{ $price->name }}</h3>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('material-prices.edit', $price->id) }}" class="p-1.5 text-slate-400 hover:text-primary">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </a>
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
