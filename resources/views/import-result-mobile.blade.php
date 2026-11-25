@extends('layouts.dashboard')

@section('title', 'Xem trước dữ liệu nhập')
@section('page_heading', 'Nhập hóa đơn Excel')

@section('content')
<div class="mx-auto flex w-full max-w-md flex-col gap-4 pb-36">
    <div>
        <a href="{{ route('invoice.index', array_filter(['project_id' => $projectId ?? null])) }}" class="-ml-1 inline-flex min-h-[44px] items-center gap-1 text-sm font-semibold text-gray-500 hover:text-primary dark:text-gray-400">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
            Chọn file khác
        </a>
        <h1 class="text-2xl font-black tracking-tight text-text-light dark:text-text-dark">Xem trước dữ liệu</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kiểm tra lại thông tin trước khi lưu vào hệ thống.</p>
    </div>

    {{-- Khách hàng --}}
    <section class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
        <div class="mb-3 flex items-center gap-2 border-b border-border-light pb-3 dark:border-border-dark">
            <span class="material-symbols-outlined text-xl text-primary">person</span>
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-800 dark:text-white">Thông tin khách hàng</h2>
        </div>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between gap-3">
                <dt class="text-gray-500">Tên khách</dt>
                <dd class="text-right font-semibold text-gray-900 dark:text-gray-100">{{ $khachHang }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-gray-500">Địa chỉ</dt>
                <dd class="text-right text-gray-700 dark:text-gray-300">{{ $diaChi ?: '—' }}</dd>
            </div>
            <div class="flex justify-between gap-3">
                <dt class="text-gray-500">Điện thoại</dt>
                <dd class="text-right font-semibold text-gray-900 dark:text-gray-100">{{ $dienThoai ?: '—' }}</dd>
            </div>
        </dl>
    </section>

    {{-- Tổng --}}
    <section class="rounded-xl border border-primary/20 bg-primary/5 p-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-500">Ngày hóa đơn cuối</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $ngayCuoi }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-semibold text-gray-500">Tổng tiền</p>
                <p class="text-lg font-black text-primary">{{ number_format($tongTien, 0, ',', '.') }} đ</p>
            </div>
        </div>
        <p class="mt-3 border-t border-primary/10 pt-3 text-center text-xs italic text-gray-500">{{ $tongTienChu }}</p>
    </section>

    {{-- Các khoản ứng tiền --}}
    @if(!empty($advancePayments))
        <section class="space-y-3">
            <h2 class="px-1 text-xs font-bold uppercase tracking-widest text-green-600">Các khoản ứng tiền ({{ count($advancePayments) }})</h2>
            @foreach($advancePayments as $ap)
                <div class="flex items-center justify-between gap-3 rounded-xl border border-green-100 bg-green-50 p-4 dark:border-green-800/30 dark:bg-green-900/10">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-100">{{ $ap['noi_dung'] }}</p>
                        <p class="text-xs italic text-gray-500">{{ $ap['ngay_thang'] ?: '—' }}</p>
                    </div>
                    <span class="shrink-0 text-sm font-black text-green-600 dark:text-green-400">{{ number_format($ap['so_tien'], 0, ',', '.') }} đ</span>
                </div>
            @endforeach
        </section>
    @endif

    {{-- Vật tư --}}
    <section class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-xs font-bold uppercase tracking-widest text-gray-500">Danh sách vật tư ({{ count($items) }})</h2>
            <span class="rounded bg-gray-200 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">Đã kiểm tra</span>
        </div>

        @foreach($items as $index => $item)
            <div class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <div class="mb-2 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-400">#{{ $item['stt'] ?? ($index + 1) }}</span>
                            @if($item['ngay_thang'])
                                <span class="rounded bg-primary/10 px-1.5 py-0.5 text-xs font-medium text-primary">{{ $item['ngay_thang'] }}</span>
                            @endif
                        </div>
                        <h3 class="mt-1 font-bold text-gray-900 dark:text-white">{{ $item['ten_vat_lieu'] }}</h3>
                    </div>
                    <span class="shrink-0 text-sm font-black text-primary">{{ number_format($item['thanh_tien'], 0, ',', '.') }} đ</span>
                </div>
                <dl class="mt-2 grid grid-cols-3 gap-2 border-t border-border-light pt-3 text-xs dark:border-border-dark">
                    <div>
                        <dt class="text-gray-400">ĐVT</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $item['don_vi_tinh'] ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400">Số lượng</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($item['so_luong'], 2, ',', '.') }}</dd>
                    </div>
                    <div class="text-right">
                        <dt class="text-gray-400">Đơn giá</dt>
                        <dd class="font-medium text-gray-700 dark:text-gray-300">{{ number_format($item['don_gia'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        @endforeach
    </section>

    <x-import-project-fields :projects="$projects" :project-id="$projectId" form-id="import-save-form" />

    {{-- Thanh lưu dính đáy --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-border-light bg-container-light/95 p-4 pb-[calc(1rem+env(safe-area-inset-bottom))] backdrop-blur-md dark:border-border-dark dark:bg-container-dark/95 lg:left-64">
        <div class="mx-auto flex max-w-md flex-col gap-2">
            <form id="import-save-form" action="{{ route('invoice.save') }}" method="POST" @submit="isLoading = true">
                @csrf
                <input type="hidden" name="khachHang" value="{{ $khachHang }}">
                <input type="hidden" name="diaChi" value="{{ $diaChi }}">
                <input type="hidden" name="dienThoai" value="{{ $dienThoai }}">
                <input type="hidden" name="tongTien" value="{{ $tongTien }}">
                <input type="hidden" name="tongTienChu" value="{{ $tongTienChu }}">
                <input type="hidden" name="ngayCuoi" value="{{ $ngayCuoi }}">
                <input type="hidden" name="items" value="{{ base64_encode(json_encode($items)) }}">
                <input type="hidden" name="advance_payments" value="{{ base64_encode(json_encode($advancePayments ?? [])) }}">

                <button type="submit" class="flex min-h-[48px] w-full items-center justify-center gap-2 rounded-lg bg-green-600 text-sm font-bold text-white shadow-lg shadow-green-600/20 active:scale-[0.98]">
                    <span class="material-symbols-outlined text-lg">cloud_upload</span>
                    Lưu hóa đơn vào hệ thống
                </button>
            </form>
            <a href="{{ route('invoice.index', ['project_id' => $projectId ?? '']) }}" class="flex min-h-[44px] items-center justify-center text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400">
                Hủy bỏ
            </a>
        </div>
    </div>
</div>
@endsection
