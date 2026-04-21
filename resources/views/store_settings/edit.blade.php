@extends('layouts.dashboard')

@section('title', 'Thông tin cửa hàng')

@section('content')
    <div class="flex flex-col gap-6 max-w-3xl">
        @if(session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Thông tin cửa hàng</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Dùng cho trang chủ, file Excel báo giá và có thể tái sử dụng khi in phiếu / PDF sau này.
            </p>
        </div>

        <form method="POST" action="{{ route('store-settings.update') }}" class="space-y-5 bg-container-light dark:bg-container-dark rounded-xl shadow-subtle border border-border-light dark:border-border-dark p-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="name">Tên hiển thị <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $storeInfo->name) }}" required
                       class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"/>
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="address">Địa chỉ</label>
                <textarea name="address" id="address" rows="2"
                          class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark">{{ old('address', $storeInfo->address) }}</textarea>
                @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="phone">Điện thoại / Hotline</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $storeInfo->phone) }}"
                       class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"/>
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="bank_name">Ngân hàng</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $storeInfo->bank_name) }}"
                           class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="bank_account">Số tài khoản</label>
                    <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $storeInfo->bank_account) }}"
                           class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"/>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="bank_owner">Chủ tài khoản</label>
                <input type="text" name="bank_owner" id="bank_owner" value="{{ old('bank_owner', $storeInfo->bank_owner) }}"
                       class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"/>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="note">Ghi chú (in / PDF)</label>
                <textarea name="note" id="note" rows="3" placeholder="Ví dụ: MST, dòng chú thích dưới báo giá..."
                          class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark">{{ old('note', $storeInfo->note) }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors">
                    Lưu
                </button>
            </div>
        </form>
    </div>
@endsection
