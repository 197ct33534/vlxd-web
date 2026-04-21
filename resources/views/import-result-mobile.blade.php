<!DOCTYPE html>
<html lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', 'Import Invoice - Data Preview Mobile | CMS Inc.')</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#2563eb",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                        "sidebar-light": "#ffffff",
                        "sidebar-dark": "#1e293b",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
    </script>
<style type="text/tailwindcss">
        @layer base {
            body {
                @apply font-sans;
            }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
    </style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen" x-data="{ loading: false }">
<nav class="sticky top-0 z-30 bg-white dark:bg-sidebar-dark border-b border-slate-200 dark:border-slate-800 px-4 h-16 flex items-center justify-between">
<div class="flex items-center gap-3">
<a href="{{ route('invoice.index') }}" class="inline-flex max-w-[55%] items-center gap-1.5 -ml-2 rounded-lg p-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
<span class="material-symbols-outlined shrink-0">arrow_back</span>
<span class="truncate text-xs font-semibold">{{ __('nav.back_short') }}</span>
</a>
<div class="flex items-center gap-2">
<div class="w-7 h-7 bg-primary rounded flex items-center justify-center text-white">
<span class="material-symbols-outlined text-sm">receipt_long</span>
</div>
<span class="font-bold text-slate-800 dark:text-white tracking-tight">CMS Inc.</span>
</div>
</div>
</nav>
<main class="p-4 pb-32">
<div class="mb-6 text-center">
<div class="inline-flex items-center justify-center p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-3">
<span class="material-symbols-outlined text-primary text-2xl">task_alt</span>
</div>
<h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">Kết quả đọc file hóa đơn</h1>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 px-4">Vui lòng kiểm tra lại thông tin trước khi xác nhận nhập vào hệ thống.</p>
</div>
<div class="space-y-4 mb-6">
<div class="bg-white dark:bg-sidebar-dark rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
<div class="flex items-center gap-2 mb-4 border-b border-slate-50 dark:border-slate-800 pb-3">
<span class="material-symbols-outlined text-primary text-xl">person_outline</span>
<h3 class="font-bold text-xs text-slate-800 dark:text-white uppercase tracking-wider">Thông tin khách hàng</h3>
</div>
<div class="space-y-3">
<div class="flex justify-between items-center text-sm">
<span class="text-slate-500">Tên khách:</span>
<span class="font-semibold text-slate-900 dark:text-slate-100">{{ $khachHang }}</span>
</div>
<div class="flex justify-between items-start text-sm">
<span class="text-slate-500">Địa chỉ:</span>
<span class="text-slate-700 dark:text-slate-300 text-right">{{ $diaChi ?: '—' }}</span>
</div>
<div class="flex justify-between items-center text-sm">
<span class="text-slate-500">Điện thoại:</span>
<span class="font-semibold text-slate-900 dark:text-slate-100">{{ $dienThoai ?: '—' }}</span>
</div>
</div>
</div>
<div class="bg-primary/5 dark:bg-blue-900/10 rounded-xl border border-primary/10 dark:border-blue-800/30 p-4">
<div class="grid grid-cols-2 gap-4">
<div class="space-y-1">
<span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Ngày hóa đơn cuối</span>
<p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $ngayCuoi }}</p>
</div>
<div class="space-y-1 text-right">
<span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Tổng tiền</span>
<p class="text-lg font-black text-primary">{{ number_format($tongTien, 0, ',', '.') }} đ</p>
</div>
</div>
<div class="mt-3 pt-3 border-t border-primary/10 dark:border-blue-800/30 text-center">
<p class="text-[10px] text-slate-500 italic">{{ $tongTienChu }}</p>
</div>
</div>
</div>

{{-- Các khoản ứng tiền (nếu có) --}}
@if(!empty($advancePayments))
<div class="mb-6 space-y-3">
    <h3 class="font-bold text-xs text-emerald-500 uppercase tracking-widest px-1">Các khoản ứng tiền ({{ count($advancePayments) }})</h3>
    @foreach($advancePayments as $ap)
    <div class="bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-800/30 p-4 flex justify-between items-center">
        <div class="flex flex-col">
            <span class="text-xs font-bold text-slate-800 dark:text-slate-100">{{ $ap['noi_dung'] }}</span>
            <span class="text-[10px] text-slate-500 italic">{{ $ap['ngay_thang'] ?: '—' }}</span>
        </div>
        <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($ap['so_tien'], 0, ',', '.') }} đ</span>
    </div>
    @endforeach
</div>
@endif

<div class="space-y-3">
<div class="flex items-center justify-between mb-2 px-1">
<h3 class="font-bold text-xs text-slate-500 uppercase tracking-widest">Danh sách vật tư ({{ count($items) }})</h3>
<span class="text-[10px] bg-slate-200 dark:bg-slate-800 px-2 py-0.5 rounded text-slate-600 dark:text-slate-400">Đã kiểm tra</span>
</div>

@foreach($items as $index => $item)
<div class="bg-white dark:bg-sidebar-dark rounded-xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm relative overflow-hidden">
<div class="absolute top-0 left-0 w-1 h-full bg-primary/20"></div>
<div class="flex justify-between items-start mb-2">
<div class="flex flex-col">
<div class="flex items-center gap-2">
<span class="text-[10px] text-slate-400 font-bold">#{{ $item['stt'] ?? ($index + 1) }}</span>
@if($item['ngay_thang'])
<span class="text-[10px] bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 px-1.5 py-0.5 rounded font-medium">{{ $item['ngay_thang'] }}</span>
@endif
</div>
<h4 class="font-bold text-slate-900 dark:text-white mt-1">{{ $item['ten_vat_lieu'] }}</h4>
</div>
<span class="text-xs font-black text-primary">{{ number_format($item['thanh_tien'], 0, ',', '.') }} đ</span>
</div>
<div class="flex items-center gap-4 text-xs text-slate-500 border-t border-slate-50 dark:border-slate-800 pt-3 mt-2">
<div class="flex flex-col">
<span class="text-[10px] uppercase text-slate-400">ĐVT</span>
<span class="font-medium text-slate-700 dark:text-slate-300">{{ $item['don_vi_tinh'] ?: '—' }}</span>
</div>
<div class="flex flex-col">
<span class="text-[10px] uppercase text-slate-400">Số lượng</span>
<span class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($item['so_luong'], 2, ',', '.') }}</span>
</div>
<div class="flex flex-col ml-auto text-right">
<span class="text-[10px] uppercase text-slate-400">Đơn giá</span>
<span class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($item['don_gia'], 0, ',', '.') }}</span>
</div>
</div>
</div>
@endforeach

</div>
<footer class="mt-8 text-center text-slate-400 text-[10px] pb-10">
<p>© {{ date('Y') }} CMS Inc. | v1.0.0</p>
</footer>
</main>

<div class="fixed bottom-0 left-0 right-0 bg-white/95 dark:bg-sidebar-dark/95 backdrop-blur-sm border-t border-slate-200 dark:border-slate-800 p-4 pb-6 z-40 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)]">
<div class="max-w-md mx-auto flex flex-col gap-3">
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

<button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-lg">cloud_upload</span>
                Tiến hành import vào database
            </button>
</form>
<a href="{{ route('invoice.index', ['project_id' => $projectId ?? '']) }}" class="w-full text-center text-slate-500 dark:text-slate-400 py-2 text-sm font-medium hover:text-slate-800 transition-colors">
                Hủy bỏ
            </a>
</div>
</div>

{{-- Loading Overlay --}}
<div x-cloak x-show="loading"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center z-[100]"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white dark:bg-slate-800 p-10 rounded-2xl shadow-2xl text-center max-w-sm w-full mx-4 transform transition-all animate-in zoom-in duration-300">
        <div class="flex justify-center mb-6">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-emerald-100 border-t-emerald-600 rounded-full animate-spin"></div>
            </div>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 uppercase tracking-wide">Đang lưu dữ liệu</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Hệ thống đang đồng bộ hóa đơn của bạn. Vui lòng không tắt màn hình.</p>
    </div>
</div>

<div class="fixed bottom-32 right-4 z-50">
<button class="w-10 h-10 bg-white dark:bg-sidebar-dark shadow-xl rounded-full border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-yellow-400 transition-transform active:scale-90" onclick="document.documentElement.classList.toggle('dark')">
<span class="material-symbols-outlined text-xl">dark_mode</span>
</button>
</div>

</body></html>
