@extends('layouts.mobile-customer')

@section('title', 'Giao hàng')
@section('heading', 'Đơn giao hôm nay')

@section('bottom_nav')
<nav class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 pb-[env(safe-area-inset-bottom)]">
    <div class="max-w-lg mx-auto flex justify-around text-xs font-semibold py-2">
        <a href="{{ route('driver.index') }}" class="text-primary flex flex-col items-center mf-touch">
            <span class="material-symbols-outlined">local_shipping</span>
            Đơn giao
        </a>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button class="flex flex-col items-center text-gray-500 mf-touch">
                <span class="material-symbols-outlined">logout</span>
                Thoát
            </button>
        </form>
    </div>
</nav>
@endsection

@section('content')
<div class="space-y-3 pb-8">
    @forelse($orders as $order)
        <a href="{{ route('driver.show', $order) }}" class="block rounded-2xl bg-white border border-gray-200 p-4">
            <div class="flex justify-between gap-3">
                <div>
                    <p class="font-bold">{{ $order->code }}</p>
                    <p class="text-sm">{{ $order->customer?->name }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $order->delivery_address }}</p>
                </div>
                <span class="text-xs font-bold h-fit rounded-full px-2 py-1 {{ $order->status === 'delivering' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $order->statusLabel() }}
                </span>
            </div>
        </a>
    @empty
        <div class="rounded-2xl border border-dashed p-6 text-center text-gray-500 text-sm">Không có đơn giao.</div>
    @endforelse
</div>
@endsection
