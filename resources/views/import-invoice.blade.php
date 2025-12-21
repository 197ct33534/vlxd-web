@extends('layouts.app')

@section('title', 'Import Hóa Đơn')

@section('content')
<div x-data="{ loading: false }" class="max-w-lg mx-auto bg-white shadow rounded-lg p-8 relative">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">📂 Import Hóa Đơn Bán Hàng</h2>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form import --}}
    <form action="{{ route('invoice.import') }}" method="POST" enctype="multipart/form-data"
          @submit="loading = true"
          class="space-y-4">
        @csrf
        <div>
            <label class="block mb-1 font-medium text-gray-700">Chọn file Excel (.xlsx, .xls)</label>
            <input type="file" name="file" accept=".xlsx,.xls"
                   class="block w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-200" required>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition">
            📥 Tiến hành Import
        </button>
    </form>

    {{-- Popup loading overlay --}}
    <div x-show="loading"
         class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50"
         x-transition>
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <div class="flex justify-center mb-3">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
            <p class="text-gray-700 font-medium">Đang xử lý file, vui lòng chờ...</p>
        </div>
    </div>
</div>
@endsection
