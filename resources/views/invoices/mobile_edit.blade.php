<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Chỉnh sửa hóa đơn - {{ $invoice->code ?? '#' . $invoice->id }}</title>
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
            <span class="text-slate-700 dark:text-slate-200 font-medium text-sm">Đang xử lý...</span>
        </div>
    </div>

    <header class="bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 h-16 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" @click="isLoading = true" class="p-2 -ml-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                <span class="material-symbols-outlined text-2xl">arrow_back</span>
            </a>
            <div class="bg-primary rounded-lg p-1.5 text-white">
                <span class="material-symbols-outlined text-xl">edit_document</span>
            </div>
            <h1 class="text-lg font-bold text-gray-800 dark:text-white">Chỉnh sửa hóa đơn</h1>
        </div>
        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" @submit="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này?');" class="flex items-center">
            @csrf
            @method('DELETE')
            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                <span class="material-symbols-outlined">delete</span>
            </button>
        </form>
    </header>

    <main class="flex-1 pb-24 p-4 max-w-2xl mx-auto w-full">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Cập nhật hóa đơn</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hóa đơn: <span class="text-primary font-bold">{{ $invoice->code ?? '#' . $invoice->id }}</span></p>
            <p class="text-xs text-gray-400">Dự án: {{ $invoice->project->name }}</p>
        </div>

        <form id="editInvoiceForm" action="{{ route('invoices.update', $invoice->id) }}" method="POST" @submit="isLoading = true">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Info Section -->
                <section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-bold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">description</span>
                        Thông tin chi tiết
                    </h3>
                    <div class="space-y-4">
                        <label class="flex flex-col">
                            <span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Ngày xuất</span>
                            <input class="form-input w-full rounded-lg text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary px-4" 
                                type="date" name="invoice_date" value="{{ $invoice->invoice_date }}" required/>
                        </label>
                        <label class="flex flex-col">
                            <span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Mã hóa đơn</span>
                            <input class="form-input w-full rounded-lg text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary px-4" 
                                type="text" name="code" value="{{ $invoice->code }}" placeholder="VD: HD-001"/>
                        </label>
                    </div>
                </section>

                <!-- Items Section -->
                <section class="space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-xl">list_alt</span>
                            Danh sách sản phẩm
                        </h3>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="`${items.length} mục` text-gray-500">0 mục</span>
                    </div>

                    <template x-for="(item, index) in items" :key="index">
                        <div class="bg-white dark:bg-gray-900/50 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 relative">
                            <button type="button" @click="removeItem(index)" class="absolute top-4 right-4 text-red-500 p-1 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" x-show="items.length > 1">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Tên sản phẩm</label>
                                    <input class="w-full bg-transparent border-none p-0 text-sm font-semibold text-gray-900 dark:text-white focus:ring-0" 
                                        placeholder="Nhập tên sản phẩm..." type="text" :name="`items[${index}][product_name]`" x-model="item.product_name" required/>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Đơn vị</span>
                                        <input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2" 
                                            placeholder="kg, m..." type="text" :name="`items[${index}][unit]`" x-model="item.unit"/>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Số lượng</span>
                                        <input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2 text-center" 
                                            placeholder="0" type="number" step="any" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required/>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Đơn giá</span>
                                        <input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2 text-right" 
                                            placeholder="0" type="number" step="any" :name="`items[${index}][price]`" x-model.number="item.price" required/>
                                    </div>
                                </div>
                                <div class="flex justify-end pt-2 text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase mr-2 mt-1">Thành tiền:</span>
                                    <span class="text-sm font-bold text-primary" x-text="formatNumber(item.quantity * item.price) + 'đ'"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addItem()" class="w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-primary font-bold text-sm hover:bg-primary/5 transition-colors">
                        <span class="material-symbols-outlined text-base">add_circle</span>
                        Thêm sản phẩm
                    </button>
                </section>

                <section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
                    <label class="flex flex-col">
                        <span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Ghi chú</span>
                        <textarea class="form-textarea w-full rounded-lg text-gray-900 dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-24 text-sm focus:ring-primary focus:border-primary p-3" 
                            name="note" placeholder="Thêm ghi chú...">{{ $invoice->note }}</textarea>
                    </label>
                </section>

                <!-- Summary Section -->
                <section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Tạm tính</span>
                        <span class="font-semibold text-gray-900 dark:text-white" x-text="formatNumber(totalAmount) + 'đ'">0đ</span>
                    </div>
                    @if($invoice->total_text)
                    <div class="text-right text-[10px] text-gray-400 italic">
                        ({{ $invoice->total_text }})
                    </div>
                    @endif
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between items-center">
                        <span class="text-base font-bold text-gray-900 dark:text-white">Tổng cộng</span>
                        <span class="text-xl font-black text-primary" x-text="formatNumber(totalAmount) + 'đ'">0đ</span>
                    </div>
                </section>

                <div class="flex flex-col gap-3 pb-8">
                    <button type="submit" form="editInvoiceForm" class="w-full py-4 rounded-xl text-base font-bold bg-primary text-white hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                        Lưu thay đổi
                    </button>
                    <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" @click="isLoading = true" class="w-full py-4 rounded-xl text-base font-semibold text-center bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        Hủy
                    </a>
                </div>
            </div>
        </form>
    </main>
    
    <footer class="text-center py-6 px-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
        <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest">© {{ date('Y') }} Invoice Inc. | Dự án: {{ $invoice->project->name }}</p>
    </footer>

    <script>
        function invoiceForm() {
            return {
                isLoading: false,
                items: @json($invoice->items).map(function(item) {
                    return {
                        product_name: item.product_name,
                        unit: item.unit,
                        quantity: parseFloat(item.quantity) || 0,
                        price: parseFloat(item.price) || 0
                    };
                }),
                
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
