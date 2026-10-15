@extends('layouts.mobile-customer')

@section('title', 'Đăng nhập VLXD')
@section('heading', 'Xin chào')

@section('bottom_nav')
@endsection

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white border border-gray-200 p-5">
        <p class="text-sm text-gray-600 mb-4">
            Nhập số điện thoại đã đăng ký với cửa hàng để xem công nợ, đặt hàng và theo dõi xe giao.
        </p>
        <form method="POST" action="{{ route('portal.login.submit') }}" class="space-y-4">
            @csrf
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Số điện thoại</span>
                <input
                    type="tel"
                    name="phone"
                    inputmode="tel"
                    autocomplete="tel"
                    value="{{ old('phone') }}"
                    required
                    class="w-full rounded-xl border-gray-300 text-lg mf-touch focus:border-primary focus:ring-primary"
                    placeholder="09xx xxx xxx"
                />
            </label>
            @error('phone')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <button type="submit" class="mf-cta-btn mf-cta-btn--primary w-full">
                Vào ứng dụng
            </button>
        </form>
    </div>
    <p class="text-center text-sm text-gray-500">
        Chưa có tài khoản? Bấm <strong>Gọi shop</strong> hoặc <strong>Zalo</strong> bên dưới.
    </p>
</div>
@endsection
