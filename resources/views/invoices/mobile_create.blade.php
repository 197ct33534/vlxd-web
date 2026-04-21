<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Tạo hóa đơn mới - {{ $project->name }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
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
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    
    <style type="text/tailwindcss">
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        @media (max-width: 768px) {
            .mobile-hide { display: none; }
            .sticky-actions {
                position: sticky;
                bottom: 0;
                background: white;
                padding: 1rem;
                box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1);
                z-index: 40;
            }
        }
        [x-cloak] { display: none !important; }
    </style>
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-display bg-background-light dark:bg-background-dark min-h-screen flex flex-col" 
    x-data="invoiceForm()"
    x-init="isLoading = false"
>
    <!-- Loading Indicator -->
    <div x-cloak x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-4">
            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-slate-700 dark:text-slate-200 font-medium text-sm">{{ __('common.loading') }}</span>
        </div>
    </div>

    <header class="bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 h-16 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.invoices.index', $project->id) }}" @click="isLoading = true" class="inline-flex max-w-[40%] items-center gap-1.5 -ml-2 rounded-lg p-2 text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                <span class="material-symbols-outlined shrink-0 text-2xl">arrow_back</span>
                <span class="truncate text-xs font-semibold">{{ __('nav.back_short') }}</span>
            </a>
            <div class="bg-primary rounded-lg p-1.5 text-white">
                <span class="material-symbols-outlined text-xl">receipt_long</span>
            </div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-white">Tạo hóa đơn</h1>
        </div>
    </header>

    <main class="flex-1 pb-24 p-4 max-w-2xl mx-auto w-full">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hóa đơn mới</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Dự án: <span class="text-primary font-bold">{{ $project->name }}</span></p>
        </div>

        <form id="invoiceForm" action="{{ route('projects.invoices.store', $project->id) }}" method="POST" @submit="isLoading = true">
            @csrf
            
            <div class="space-y-6">
                <!-- Info Section -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ngày xuất</label>
                        <input type="date" name="invoice_date" required value="{{ date('Y-m-d') }}"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white h-12 px-4 focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mã hóa đơn (Tùy chọn)</label>
                        <input type="text" name="code" placeholder="VD: HD-001"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white h-12 px-4 focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ghi chú</label>
                        <textarea name="note" rows="2" placeholder="Ghi chú về đơn hàng..."
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2 focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Danh mục sản phẩm</h2>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="`${items.length} mục` text-gray-500 uppercase tracking-wider">0 Mục</span>
                    </div>

                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 relative">
                            <button type="button" @click="removeItem(index)" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors" x-show="items.length > 1">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Tên sản phẩm</label>
                                    <input class="w-full border-0 p-0 text-base font-semibold text-gray-800 dark:text-white bg-transparent focus:ring-0" 
                                        placeholder="Nhập tên sản phẩm..." type="text" :name="`items[${index}][product_name]`" x-model="item.product_name" required/>
                                </div>
                                <div class="grid grid-cols-3 gap-4 border-t border-gray-100 dark:border-gray-700 pt-3">
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Đơn vị</label>
                                        <input class="w-full border-0 p-0 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" 
                                            placeholder="kg, m..." type="text" :name="`items[${index}][unit]`" x-model="item.unit"/>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Số lượng</label>
                                        <input class="w-full border-0 p-0 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" 
                                            placeholder="0" type="number" step="any" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required/>
                                    </div>
                                    <div class="text-right">
                                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Đơn giá</label>
                                        <input class="w-full border-0 p-0 text-right text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" 
                                            placeholder="0" type="number" step="any" :name="`items[${index}][price]`" x-model.number="item.price" required/>
                                    </div>
                                </div>
                                <div class="flex justify-end pt-2 text-right">
                                    <span class="text-xs font-bold text-slate-400 uppercase mr-2 mt-0.5">Thành tiền:</span>
                                    <span class="text-sm font-bold text-primary" x-text="formatNumber(item.quantity * item.price) + 'đ'"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="w-full py-4 flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-primary font-bold hover:bg-primary/5 transition-colors">
                        <span class="material-symbols-outlined">add_circle</span>
                        Thêm sản phẩm
                    </button>
                </div>

                <!-- Summary -->
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-3">
                    <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                        <span>Tổng tiền hàng</span>
                        <span class="font-medium" x-text="formatNumber(totalAmount) + 'đ'">0đ</span>
                    </div>
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900 dark:text-white">Tổng cộng</span>
                        <span class="text-xl font-black text-primary" x-text="formatNumber(totalAmount) + 'đ'">0đ</span>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <div class="sticky-actions flex gap-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
        <a href="{{ route('projects.invoices.index', $project->id) }}" @click="isLoading = true" class="flex-1 py-4 rounded-xl text-sm font-bold text-center text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800">
            Hủy
        </a>
        <button type="submit" form="invoiceForm" class="flex-[2] py-4 rounded-xl text-sm font-bold text-white bg-primary shadow-lg shadow-primary/25">
            Lưu hóa đơn
        </button>
    </div>

    <script>
        function invoiceForm() {
            return {
                isLoading: false,
                items: [
                    { product_name: '', unit: '', quantity: 1, price: 0 }
                ],
                
                addItem() {
                    this.items.push({ product_name: '', unit: '', quantity: 1, price: 0 });
                },
                
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                
                get totalAmount() {
                    return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                },
                
                formatNumber(num) {
                    return new Intl.NumberFormat('vi-VN').format(num);
                }
            }
        }
    </script>
</body>
</html>
