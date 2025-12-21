@extends('layouts.app')

@section('title', 'Kết quả nhập hóa đơn')

@section('content')
    <div x-data="{ loading: false }" class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 relative">

        <h1 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-blue-700 flex items-center justify-center gap-2">
            <span>🧾</span> Kết quả đọc file hóa đơn
        </h1>

        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Khách hàng: {{ $khachHang }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700">
                    <p><span class="font-medium">Địa chỉ: </span> {{ $diaChi }}</p>
                    <p><span class="font-medium">Điện thoại:</span> {{ $dienThoai }}</p>
                    <p><span class="font-medium">Ngày hóa đơn cuối:</span> {{ $ngayCuoi }}</p>
                    <p><span class="font-medium">Tổng tiền:</span>
                        <span class="font-bold text-blue-700">{{ number_format($tongTien, 0, ',', '.') }} đ</span>
                    </p>
                </div>
                <p class="italic text-gray-500 mt-2">{{ $tongTienChu }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="border-b p-3 text-center font-semibold">STT</th>
                            <th class="border-b p-3 text-center font-semibold">Ngày</th>
                            <th class="border-b p-3 text-left font-semibold">Tên vật liệu</th>
                            <th class="border-b p-3 text-center font-semibold">ĐVT</th>
                            <th class="border-b p-3 text-right font-semibold">Số lượng</th>
                            <th class="border-b p-3 text-right font-semibold">Đơn giá</th>
                            <th class="border-b p-3 text-right font-semibold">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="border-b p-3 text-center">{{ $item['stt'] }}</td>
                                <td class="border-b p-3 text-center">{{ $item['ngay_thang'] }}</td>
                                <td class="border-b p-3">{{ $item['ten_vat_lieu'] }}</td>
                                <td class="border-b p-3 text-center">{{ $item['don_vi_tinh'] }}</td>
                                <td class="border-b p-3 text-right">{{ $item['so_luong'] }}</td>
                                <td class="border-b p-3 text-right">{{ number_format($item['don_gia'], 0, ',', '.') }}</td>
                                <td class="border-b p-3 text-right font-medium text-blue-700">
                                    {{ number_format($item['thanh_tien'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <form action="{{ route('invoice.save') }}" method="POST" class="mt-8 text-center" @submit="loading = true">
                @csrf
                <input type="hidden" name="khachHang" value="{{ $khachHang }}">
                <input type="hidden" name="diaChi" value="{{ $diaChi }}">
                <input type="hidden" name="dienThoai" value="{{ $dienThoai }}">
                <input type="hidden" name="tongTien" value="{{ $tongTien }}">
                <input type="hidden" name="tongTienChu" value="{{ $tongTienChu }}">
                <input type="hidden" name="ngayCuoi" value="{{ $ngayCuoi }}">
                <input type="hidden" name="items" value="{{ base64_encode(json_encode($items)) }}">

                <button type="submit"
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center gap-2 mx-auto">
                    <span>✅</span> Tiến hành import vào database
                </button>
            </form>
        </div>

        <!-- Overlay loading -->
        <div x-show="loading" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-60 z-50"
            x-transition>
            <div class="bg-white rounded-lg p-6 shadow-lg text-center">
                <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <p class="text-gray-700 font-medium">Đang lưu dữ liệu vào database...</p>
            </div>
        </div>
    </div>
@endsection
