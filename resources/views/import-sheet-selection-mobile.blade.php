<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>@yield('title', 'CMS Inc. - Select Sheet Mobile')</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style type="text/tailwindcss">
        :root {
            --primary: #2563eb;
            --background-light: #f8fafc;
            --background-dark: #0f172a;
            --sidebar-light: #ffffff;
            --sidebar-dark: #1e293b;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        .custom-radio:checked + label {
            border-color: var(--primary);
            background-color: rgba(37, 99, 235, 0.05);
        }
        .dark .custom-radio:checked + label {
            border-color: var(--primary);
            background-color: rgba(37, 99, 235, 0.1);
        }
        .custom-radio:checked + label .radio-dot {
            transform: scale(1);
            border-color: var(--primary);
        }
        [x-cloak] { display: none !important; }
    </style>
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
                },
            },
        };
    </script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex flex-col" x-data="{ loading: false, selectedIndex: 0 }">
<header class="h-16 bg-white dark:bg-sidebar-dark border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 sticky top-0 z-50 transition-colors duration-200">
<div class="flex items-center gap-3">
<button type="button" class="inline-flex max-w-[9rem] items-center gap-1 -ml-2 rounded-lg p-2 text-slate-600 dark:text-slate-300">
<span class="material-symbols-outlined shrink-0">menu</span>
<span class="truncate text-xs font-semibold"></span>
</button>
<div class="flex items-center gap-2">
<div class="bg-primary p-1.5 rounded-lg">
<span class="material-symbols-outlined text-white text-xl">business_center</span>
</div>
<span class="text-lg font-bold tracking-tight text-slate-800 dark:text-white">CMS Inc.</span>
</div>
</div>
<div class="flex items-center gap-2">
<button type="button" class="inline-flex max-w-[8rem] items-center gap-1 rounded-lg p-2 text-slate-500 dark:text-slate-400">
<span class="material-symbols-outlined shrink-0">search</span>
<span class="truncate text-xs font-semibold">{{ __('common.search') }}</span>
</button>
<div class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 overflow-hidden border border-slate-300 dark:border-slate-600">
<span class="material-symbols-outlined text-xl">person</span>
</div>
</div>
</header>
<main class="flex-1 flex flex-col px-4 py-6 relative overflow-x-hidden">
<div class="absolute top-0 left-0 w-full h-full pointer-events-none -z-10">
<div class="absolute top-20 -left-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
<div class="absolute bottom-20 -right-20 w-64 h-64 bg-primary/5 rounded-full blur-3xl"></div>
</div>
<div class="w-full max-w-md mx-auto">
<div class="text-center mb-6">
<h1 class="text-xl font-bold text-slate-900 dark:text-white">Select Sheet to Import</h1>
</div>
<div class="bg-white dark:bg-sidebar-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
<div class="text-center mb-6">
<div class="inline-flex items-center justify-center w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-2xl mb-4">
<span class="material-symbols-outlined text-2xl">folder</span>
</div>
<p class="text-sm text-slate-500 dark:text-slate-400">
                        Please select the Excel sheet you want to import data from:
                    </p>
</div>
<form action="{{ route('invoice.import') }}" method="POST" class="space-y-3" @submit="loading = true">
@csrf
<input type="hidden" name="stored_file_path" value="{{ $storedFilePath }}">
@if(isset($projectId))
    <input type="hidden" name="project_id" value="{{ $projectId }}">
@endif

@foreach($sheetNames as $index => $name)
<div class="relative">
<input type="radio" name="sheet_index" value="{{ $index }}" id="sheet_{{ $index }}" 
       class="hidden peer custom-radio" x-model="selectedIndex"
       {{ $index === 0 ? 'checked' : '' }}>
<label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30 cursor-pointer hover:border-primary/50 transition-all active:scale-[0.98]" for="sheet_{{ $index }}">
<div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center">
<div class="radio-dot w-2.5 h-2.5 rounded-full bg-primary transition-transform duration-200"
     :class="selectedIndex == {{ $index }} ? 'scale-100' : 'scale-0'"></div>
</div>
<span class="text-sm font-semibold text-slate-700 dark:text-slate-200"
      :class="selectedIndex == {{ $index }} ? 'text-primary' : ''">{{ $name }}</span>
</label>
</div>
@endforeach

<button class="w-full mt-6 bg-primary hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 active:scale-95" type="submit">
<span>Continue Import</span>
<span class="material-symbols-outlined text-[20px]">arrow_forward</span>
</button>
</form>
</div>
</div>
</main>
<nav class="h-16 bg-white dark:bg-sidebar-dark border-t border-slate-200 dark:border-slate-800 grid grid-cols-4 items-center sticky bottom-0 z-50 transition-colors duration-200">
<a class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500" href="{{ route('dashboard') }}">
<span class="material-symbols-outlined">dashboard</span>
<span class="text-[10px] mt-0.5 font-medium">Home</span>
</a>
<a class="flex flex-col items-center justify-center text-primary" href="{{ route('invoice.index') }}">
<span class="material-symbols-outlined">receipt_long</span>
<span class="text-[10px] mt-0.5 font-bold">Invoices</span>
</a>
<a class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500" href="{{ route('customers.index') }}">
<span class="material-symbols-outlined">people</span>
<span class="text-[10px] mt-0.5 font-medium">Clients</span>
</a>
<a class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-500" href="#">
<span class="material-symbols-outlined">settings</span>
<span class="text-[10px] mt-0.5 font-medium">Settings</span>
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

<footer class="py-4 text-center pb-20">
<p class="text-[10px] text-slate-400 dark:text-slate-500">
            © {{ date('Y') }} CMS Inc. | v1.0.0
        </p>
</footer>

</body></html>