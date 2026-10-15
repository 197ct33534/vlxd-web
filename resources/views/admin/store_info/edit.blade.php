@extends('layouts.dashboard')

@section('title', 'Thông tin cửa hàng')

@section('content')
<div class="max-w-2xl flex flex-col gap-6">
    <div>
        <h1 class="text-2xl font-black mf-admin-title">Thông tin cửa hàng</h1>
        <p class="text-sm text-gray-500 mt-1">Số điện thoại & Zalo hiện trên portal khách (/m).</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 text-green-800 px-4 py-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.store-info.update') }}" class="bg-white rounded-xl shadow-subtle p-5 space-y-4">
        @csrf
        @method('PUT')

        <label class="block">
            <span class="text-sm font-semibold mb-1 block">Tên cửa hàng</span>
            <input type="text" name="name" value="{{ old('name', $store->name) }}" required class="w-full rounded-lg border-gray-300 mf-touch"/>
            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </label>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">SĐT (nút Gọi)</span>
                <input type="tel" name="phone" value="{{ old('phone', $store->phone) }}" class="w-full rounded-lg border-gray-300 mf-touch" placeholder="0900..."/>
            </label>
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Zalo OA ID</span>
                <input type="text" name="zalo_oa_id" value="{{ old('zalo_oa_id', $store->zalo_oa_id) }}" class="w-full rounded-lg border-gray-300 mf-touch"/>
            </label>
        </div>

        <label class="block">
            <span class="text-sm font-semibold mb-1 block">Link Zalo OA / zalo.me</span>
            <input type="url" name="zalo_oa_url" value="{{ old('zalo_oa_url', $store->zalo_oa_url) }}" class="w-full rounded-lg border-gray-300 mf-touch" placeholder="https://zalo.me/..."/>
            @error('zalo_oa_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-500 mt-1">Để trống thì dùng https://zalo.me/{SĐT}</p>
        </label>

        <label class="block">
            <span class="text-sm font-semibold mb-1 block">Địa chỉ</span>
            <input type="text" name="address" value="{{ old('address', $store->address) }}" class="w-full rounded-lg border-gray-300 mf-touch"/>
        </label>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">STK</span>
                <input type="text" name="bank_account" value="{{ old('bank_account', $store->bank_account) }}" class="w-full rounded-lg border-gray-300 mf-touch"/>
            </label>
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Ngân hàng</span>
                <input type="text" name="bank_name" value="{{ old('bank_name', $store->bank_name) }}" class="w-full rounded-lg border-gray-300 mf-touch"/>
            </label>
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Chủ TK</span>
                <input type="text" name="bank_owner" value="{{ old('bank_owner', $store->bank_owner) }}" class="w-full rounded-lg border-gray-300 mf-touch"/>
            </label>
        </div>

        <label class="block">
            <span class="text-sm font-semibold mb-1 block">Ghi chú</span>
            <textarea name="note" rows="2" class="w-full rounded-lg border-gray-300">{{ old('note', $store->note) }}</textarea>
        </label>

        <button class="rounded-lg bg-primary text-white font-bold px-5 py-3 mf-touch">Lưu</button>
    </form>
</div>
@endsection
