<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Chi tiết hóa đơn - {{ $invoice->code ?? '#' . $invoice->id }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2a8ff4",
                        "background-light": "#f5f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            vertical-align: middle;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased font-display" x-data="{ isLoading: false }">
    
    <!-- Loading Indicator -->
    <div x-cloak x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-4">
            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-slate-700 dark:text-slate-200 font-medium text-sm">Đang xử lý...</span>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-md mx-auto min-h-screen bg-white dark:bg-slate-900 shadow-xl flex flex-col relative pb-32">
        
        <!-- Top Navigation -->
        <header class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" @click="isLoading = true" class="p-2 -ml-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
                    <span class="material-symbols-outlined text-slate-600 dark:text-slate-400">arrow_back</span>
                </a>
                <h1 class="text-lg font-bold tracking-tight">Chi tiết hóa đơn</h1>
            </div>
            <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full text-violet-600" title="Tải PDF">
                <span class="material-symbols-outlined text-slate-600 dark:text-slate-400">picture_as_pdf</span>
            </a>
        </header>

        <!-- Main Content Scroll Area -->
        <main class="flex-1 overflow-y-auto">
            <!-- Header Section: ID & Status -->
            <section class="p-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-primary mb-1 uppercase tracking-wider">Mã hóa đơn</p>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $invoice->code ?? '#' . $invoice->id }}</h2>
                    </div>
                    <span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-sm font-bold border border-emerald-200 dark:border-emerald-800">
                        Đã thanh toán
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs text-slate-500 dark:text-slate-400 uppercase font-semibold">Ngày xuất</span>
                        <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex flex-col text-right">
                        <span class="text-xs text-slate-500 dark:text-slate-400 uppercase font-semibold">Dự án</span>
                        <span class="text-sm font-medium truncate">{{ $invoice->project->name }}</span>
                    </div>
                </div>
            </section>

            <!-- Info Cards Section -->
            <section class="p-4 space-y-4">
                <!-- Customer Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-primary text-xl">person</span>
                        <h3 class="font-bold text-slate-700 dark:text-slate-300">Khách hàng</h3>
                    </div>
                    <p class="text-base font-bold text-slate-900 dark:text-white">{{ $invoice->project->customer->name ?? 'N/A' }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $invoice->project->customer->phone ?? '' }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                        {{ $invoice->project->customer->address ?? '' }}
                    </p>
                </div>

                <!-- Project Card -->
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-primary text-xl">assignment</span>
                        <h3 class="font-bold text-slate-700 dark:text-slate-300">Chi tiết dự án</h3>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-base font-bold text-slate-900 dark:text-white">{{ $invoice->project->name }}</p>
                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $invoice->project->address }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">web</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Line Items List -->
            <section class="px-4 py-2">
                <div class="flex items-center justify-between mb-4 px-2">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Sản phẩm / Dịch vụ</h3>
                    <span class="text-xs font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">{{ $invoice->items->count() }} mục</span>
                </div>
                <div class="space-y-3">
                    @foreach($invoice->items as $item)
                    <div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-slate-900 dark:text-white pr-4">{{ $item->product_name }}</h4>
                            <span class="font-bold text-slate-900 dark:text-white">{{ number_format($item->amount) }}đ</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-slate-500 dark:text-slate-400">
                            <span>Số lượng: {{ $item->quantity }} {{ $item->unit }}</span>
                            <span>{{ number_format($item->price) }}đ / đơn vị</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Financial Summary -->
            <section class="p-6 mt-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Ghi chú</span>
                        <span class="font-medium italic">{{ $invoice->note ?? '-' }}</span>
                    </div>
                    @if($invoice->total_text)
                    <div class="text-right text-xs text-slate-400 italic mb-2">
                        ({{ $invoice->total_text }})
                    </div>
                    @endif
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
                        <span class="text-lg font-bold text-slate-900 dark:text-white">Tổng cộng</span>
                        <span class="text-2xl font-black text-primary">{{ number_format($invoice->total_amount) }}đ</span>
                    </div>
                </div>
            </section>

            <!-- Footer Placeholder -->
            <div class="px-4 pb-12">
                <div class="w-full h-12 flex items-center justify-center text-xs text-slate-400">
                    <p>Hóa đơn được tạo tự động bởi hệ thống.</p>
                </div>
            </div>
        </main>

        <!-- Sticky Bottom Bar Actions -->
        <footer class="absolute bottom-0 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-4 pb-8 z-30">
            <div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
                <button onclick="window.print()" class="flex items-center justify-center gap-2 h-12 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 font-bold rounded-xl active:scale-[0.98] transition-all">
                    <span class="material-symbols-outlined text-[20px]">print</span>
                    <span>In ấn</span>
                </button>
                <button class="flex items-center justify-center gap-2 h-12 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/25 active:scale-[0.98] transition-all">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    <span>Tải PDF</span>
                </button>
            </div>
        </footer>
    </div>
</body>
</html>
