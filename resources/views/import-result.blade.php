@extends('layouts.dashboard')

@section('title', 'Kết quả nhập hóa đơn')

@section('content')
<div x-data="{ loading: false }" class="max-w-6xl mx-auto py-8 relative animate-in fade-in slide-in-from-bottom-4 duration-500">
    <!-- Page Header -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
            <span class="material-symbols-outlined text-primary text-3xl">task_alt</span>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kết quả đọc file hóa đơn</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">Vui lòng kiểm tra lại thông tin trước khi xác nhận nhập vào hệ thống.</p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-200">
        <!-- Customer Info & Summary Section -->
        <div class="p-8 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-primary">person_outline</span>
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white uppercase tracking-wide">Thông tin khách hàng</h3>
                    </div>
                    <div class="space-y-3 ml-1">
                        <div class="flex items-center gap-4">
                            <span class="text-slate-500 w-24 text-sm font-medium uppercase tracking-tighter">Tên khách:</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ $khachHang }}</span>
                        </div>
                        <div class="flex items-start gap-4">
                            <span class="text-slate-500 w-24 text-sm font-medium uppercase tracking-tighter">Địa chỉ:</span>
                            <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $diaChi ?: 'Chưa cập nhật' }}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-slate-500 w-24 text-sm font-medium uppercase tracking-tighter">Điện thoại:</span>
                            <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $dienThoai ?: '—' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="md:text-right flex flex-col justify-end">
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-100 dark:border-slate-700 inline-block ml-auto shadow-sm">
                        <div class="flex flex-col md:items-end gap-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Ngày hóa đơn cuối</span>
                            <span class="text-slate-900 dark:text-white font-bold text-lg">{{ $ngayCuoi }}</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:items-end gap-1">
                            <span class="text-[10px] font-black text-primary uppercase tracking-widest">Tổng tiền hóa đơn</span>
                            <span class="text-3xl font-black text-primary">{{ number_format($tongTien, 0, ',', '.') }} đ</span>
                            <p class="text-[10px] text-slate-400 italic font-medium">{{ $tongTienChu }}</p>
                        </div>
                        @if(isset($tongTienUng) && $tongTienUng > 0)
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:items-end gap-1">
                                <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Số tiền ứng trước</span>
                                <span class="text-xl font-bold text-emerald-500">{{ number_format($tongTienUng, 0, ',', '.') }} đ</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="py-4 px-6 text-center w-16">STT</th>
                        <th class="py-4 px-6">Ngày</th>
                        <th class="py-4 px-6 min-w-[200px]">Tên vật liệu</th>
                        <th class="py-4 px-6">ĐVT</th>
                        <th class="py-4 px-6 text-right">Số lượng</th>
                        <th class="py-4 px-6 text-right">Đơn giá</th>
                        <th class="py-4 px-6 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach ($items as $item)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                            <td class="py-4 px-6 text-center text-slate-400 text-sm font-medium">{{ $item['stt'] }}</td>
                            <td class="py-4 px-6 text-slate-500 text-sm">{{ $item['ngay_thang'] ?: '—' }}</td>
                            <td class="py-4 px-6 font-bold text-slate-800 dark:text-slate-200 group-hover:text-primary transition-colors">{{ $item['ten_vat_lieu'] }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                    {{ $item['don_vi_tinh'] ?: '—' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200 font-medium">{{ number_format($item['so_luong'], 2, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right text-slate-500 dark:text-slate-400">{{ number_format($item['don_gia'], 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right font-bold text-primary">{{ number_format($item['thanh_tien'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(!empty($advancePayments))
            <!-- Advance Payments Section -->
            <div class="p-8 bg-emerald-50/30 dark:bg-emerald-900/10 border-t border-emerald-100 dark:border-emerald-900/20">
                <div class="flex items-center gap-2 mb-6 text-emerald-600 dark:text-emerald-400">
                    <span class="material-symbols-outlined">payments</span>
                    <h3 class="font-bold text-lg uppercase tracking-wide">Các khoản ứng tiền / thanh toán</h3>
                </div>
                <div class="overflow-hidden rounded-xl border border-emerald-100 dark:border-emerald-900/30 shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700/70 dark:text-emerald-400/70 uppercase text-[10px] font-bold tracking-widest border-b border-emerald-100 dark:border-emerald-900/20">
                            <tr>
                                <th class="py-3 px-6">Ngày</th>
                                <th class="py-3 px-6">Nội dung</th>
                                <th class="py-3 px-6 text-right">Số tiền</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-900 divide-y divide-emerald-50 dark:divide-emerald-900/20">
                            @foreach ($advancePayments as $ap)
                                <tr class="hover:bg-emerald-50/50 dark:hover:bg-emerald-900/10 transition-colors">
                                    <td class="py-3 px-6 text-slate-500 text-sm">{{ $ap['ngay_thang'] ?: '—' }}</td>
                                    <td class="py-3 px-6 font-medium text-slate-800 dark:text-slate-200">{{ $ap['noi_dung'] }}</td>
                                    <td class="py-3 px-6 text-right font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ number_format($ap['so_tien'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-[11px] text-slate-500 mt-4 italic font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Các khoản ứng tiền này sẽ được lưu vào lịch sử thanh toán để trừ trực tiếp vào công nợ của khách hàng.
                </p>
            </div>
        @endif

        <!-- Footer Actions -->
        <div class="p-8 bg-slate-50/50 dark:bg-slate-800/20 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 text-sm text-slate-500 font-medium">
                <span class="material-symbols-outlined text-amber-500">info</span>
                <span>Dữ liệu đã được định dạng và sẵn sàng để lưu trữ.</span>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('invoice.index', ['project_id' => $projectId ?? '']) }}" 
                   class="px-8 py-3 rounded-xl text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-all">
                    HỦY BỎ
                </a>
                <form action="{{ route('invoice.save') }}" method="POST" @submit="loading = true">
                    @csrf
                    <input type="hidden" name="khachHang" value="{{ $khachHang }}">
                    <input type="hidden" name="diaChi" value="{{ $diaChi }}">
                    <input type="hidden" name="dienThoai" value="{{ $dienThoai }}">
                    @if(isset($projectId))
                        <input type="hidden" name="project_id" value="{{ $projectId }}">
                    @endif
                    <input type="hidden" name="tongTien" value="{{ $tongTien }}">
                    <input type="hidden" name="tongTienChu" value="{{ $tongTienChu }}">
                    <input type="hidden" name="ngayCuoi" value="{{ $ngayCuoi }}">
                    <input type="hidden" name="items" value="{{ base64_encode(json_encode($items)) }}">
                    <input type="hidden" name="advance_payments" value="{{ base64_encode(json_encode($advancePayments ?? [])) }}">

                    <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-3 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all flex items-center gap-2 transform active:scale-95 uppercase tracking-wide">
                        <span class="material-symbols-outlined text-lg">cloud_upload</span>
                        Tiến hành lưu vào database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Global Loading Overlay -->
<div x-cloak x-show="loading" class="fixed inset-0 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm z-[100]"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-2xl text-center max-w-sm w-full mx-4 transform transition-all animate-in zoom-in duration-300">
        <div class="flex justify-center mb-6">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Đang lưu dữ liệu</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Hệ thống đang đồng bộ dữ liệu hóa đơn của bạn. Vui lòng không đóng trình duyệt...</p>
    </div>
</div>
@endsection


