<!DOCTYPE html>
<html class="light" lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', 'CMS Inc. - Import Invoice Mobile')</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#2563eb",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
<style type="text/tailwindcss">
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }@media (max-width: 640px) {
            .mobile-bottom-nav {
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            }
        }
        [x-cloak] { display: none !important; }
    </style>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex flex-col" x-data="{ loading: false, fileName: '' }">
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 sticky top-0 z-30">
<div class="flex items-center gap-3">
<button type="button" class="inline-flex max-w-[9rem] items-center gap-1 -ml-2 rounded-lg p-2 text-slate-600 dark:text-slate-400">
<span class="material-icons-outlined shrink-0">menu</span>
<span class="truncate text-xs font-semibold"></span>
</button>
<div class="flex items-center gap-2">
<div class="w-7 h-7 bg-primary rounded flex items-center justify-center">
<span class="material-icons-outlined text-white text-base">business_center</span>
</div>
<span class="font-bold text-lg tracking-tight text-slate-800 dark:text-white">CMS Inc.</span>
</div>
</div>
<div class="flex items-center gap-2">
<button type="button" class="inline-flex max-w-[8rem] items-center gap-1 rounded-lg p-2 text-slate-500 dark:text-slate-400">
<span class="material-icons-outlined shrink-0">notifications</span>
<span class="truncate text-xs font-semibold">{{ __('nav.notifications') }}</span>
</button>
<div class="w-8 h-8 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center overflow-hidden border border-slate-100 dark:border-slate-800">
<span class="material-icons-outlined text-slate-500 text-xl">person</span>
</div>
</div>
</header>
<main class="flex-1 flex flex-col p-4 pb-24">
<div class="w-full max-w-md mx-auto">
<div class="text-center mb-6">
<h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">Import Hóa Đơn Bán Hàng</h2>
<p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Xử lý dữ liệu hóa đơn từ tệp Excel</p>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="p-5">
<form action="{{ route('invoice.import') }}" method="POST" enctype="multipart/form-data" @submit="loading = true" class="space-y-5">
@csrf
@if(isset($projectId))
    <input type="hidden" name="project_id" value="{{ $projectId }}">
    <div>
        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Dự án</label>
        <div class="p-3 bg-blue-50 dark:bg-primary/10 border border-primary/20 rounded-xl text-sm text-slate-700 dark:text-slate-300 font-medium">
            {{ \App\Models\Project::find($projectId)->name ?? 'Dự án #'.$projectId }}
        </div>
    </div>
@else
    <div>
    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="customer-select">Chọn Khách hàng</label>
    <div class="relative">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[20px]">person_search</span>
    <select name="customer_id" class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none text-slate-600 dark:text-slate-300" id="customer-select">
    <option disabled="" selected="" value="">Tìm kiếm hoặc chọn khách hàng</option>
    @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
    @endforeach
    </select>
    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
    </div>
    </div>
@endif

<div>
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Chọn file Excel (.xlsx, .xls)</label>
<div class="relative">
<label class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl cursor-pointer hover:border-primary transition-colors" for="file-upload">
<div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-lg flex items-center justify-center shadow-sm">
<span class="material-icons-outlined text-primary text-2xl" :class="fileName ? 'text-primary' : 'text-slate-400'">description</span>
</div>
<div class="flex-1 overflow-hidden">
<p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate" x-text="fileName ? fileName : 'Nhấn để chọn tệp tin'"></p>
<p class="text-[11px] text-slate-500 dark:text-slate-400">Excel tối đa 10MB</p>
</div>
<input name="file" accept=".xlsx, .xls" class="hidden" id="file-upload" type="file" required @change="fileName = $event.target.files[0].name"/>
<span class="material-symbols-outlined text-slate-400" :class="fileName ? 'text-primary' : ''">attach_file</span>
</label>
</div>
</div>
<button class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl flex items-center justify-center gap-2 transition-all transform active:scale-95 shadow-lg shadow-blue-200 dark:shadow-none mt-2" type="submit">
<span class="material-icons-outlined text-xl">upload_file</span>
                            Tiến hành Import
                        </button>
</form>
</div>
<div class="bg-slate-50 dark:bg-slate-800/50 px-5 py-3 border-t border-slate-100 dark:border-slate-800">
<div class="flex items-start gap-2 text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
<span class="material-icons-outlined text-sm mt-0.5">info</span>
<span>Hỗ trợ tệp Excel từ các phần mềm kế toán phổ biến.</span>
</div>
</div>
</div>
<p class="text-center mt-8 text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                © {{ date('Y') }} CMS Inc. • Version 1.0.0
            </p>
</div>
</main>
<nav class="fixed bottom-0 left-0 right-0 h-16 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-center justify-around px-2 z-30 mobile-bottom-nav">
<a class="flex flex-col items-center gap-1 text-slate-400 dark:text-slate-500" href="{{ route('dashboard') }}">
<span class="material-icons-outlined text-xl">dashboard</span>
<span class="text-[10px] font-medium">Home</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-400 dark:text-slate-500" href="{{ route('customers.index') }}">
<span class="material-icons-outlined text-xl">people_outline</span>
<span class="text-[10px] font-medium">Clients</span>
</a>
<a class="flex flex-col items-center gap-1 text-primary" href="{{ route('invoice.index') }}">
<span class="material-icons-outlined text-xl">description</span>
<span class="text-[10px] font-bold">Invoices</span>
<div class="w-1 h-1 bg-primary rounded-full"></div>
</a>
<a class="flex flex-col items-center gap-1 text-slate-400 dark:text-slate-500" href="#">
<span class="material-icons-outlined text-xl">assessment</span>
<span class="text-[10px] font-medium">Reports</span>
</a>
<a class="flex flex-col items-center gap-1 text-slate-400 dark:text-slate-500" href="#">
<span class="material-icons-outlined text-xl">settings</span>
<span class="text-[10px] font-medium">Settings</span>
</a>
</nav>

{{-- Loading Overlay --}}
<div x-cloak x-show="loading"
     class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] flex items-center justify-center z-[100]"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-2xl text-center max-w-sm w-full mx-4 transform transition-all animate-in zoom-in duration-300">
        <div class="flex justify-center mb-6">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 border-4 border-primary/20 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            </div>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">Đang xử lý dữ liệu</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Vui lòng đợi trong khi hệ thống đang xử lý tệp của bạn...</p>
    </div>
</div>

<div class="fixed bottom-20 right-4 z-20">
<button type="button" class="inline-flex h-11 max-w-[10rem] items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-slate-600 shadow-lg dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300" onclick="document.documentElement.classList.toggle('dark')" title="{{ __('common.theme_toggle') }}">
<span class="material-icons-outlined shrink-0 text-xl">dark_mode</span>
<span class="truncate text-[10px] font-bold leading-tight">{{ __('common.theme_toggle') }}</span>
</button>
</div>

</body></html>
