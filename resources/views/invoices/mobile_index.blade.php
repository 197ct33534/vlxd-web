<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Danh sách hóa đơn - {{ $project->name }}</title>
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
            -webkit-tap-highlight-color: transparent;
        }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased font-display" 
    x-data="{ 
        isLoading: false,
        activeTab: 'invoices',
        paymentModalOpen: false,
        editMode: false,
        paymentId: null,
        paymentData: { date: '{{ date('Y-m-d') }}', amount: '', note: '' },
        
        openAddPayment() {
            this.editMode = false;
            this.paymentId = null;
            this.paymentData = { date: '{{ date('Y-m-d') }}', amount: '', note: '' };
            this.paymentModalOpen = true;
        },
        openEditPayment(payment) {
            this.editMode = true;
            this.paymentId = payment.id;
            this.paymentData = { 
                date: payment.payment_date.split(' ')[0], 
                amount: payment.amount, 
                note: payment.note || '' 
            };
            this.paymentModalOpen = true;
        }
    }"
    @click="
        const link = $event.target.closest('a');
        if (link && link.href && !link.href.includes('#') && !link.getAttribute('href').startsWith('javascript:') && !link.hasAttribute('download') && link.target !== '_blank') {
            isLoading = true;
        }
    ">
    
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
    <div class="max-w-md mx-auto min-h-screen bg-white dark:bg-slate-900 flex flex-col relative pb-32">
        
        <!-- Header -->
        <header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('customers.projects.index', $project->customer_id) }}" @click="isLoading = true" class="p-2 -ml-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors font-bold text-slate-600 dark:text-slate-400">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-lg font-bold tracking-tight">Hóa đơn & Dự án</h1>
                    <p class="text-[10px] text-slate-500 font-medium truncate w-40">{{ $project->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('projects.price_history', $project->id) }}" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full text-purple-500">
                    <span class="material-symbols-outlined">trending_up</span>
                </a>
                <button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full">
                    <span class="material-symbols-outlined text-slate-600 dark:text-slate-400">more_vert</span>
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto">
            <!-- Financial Summary Card -->
            <section class="p-4">
                <div class="bg-gradient-to-br from-primary to-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-primary/20">
                    <p class="text-blue-100 text-sm font-medium mb-1">Tổng dư nợ</p>
                    <h2 class="text-3xl font-black mb-6">{{ number_format($project->total_debt) }}đ</h2>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/20">
                        <div>
                            <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider mb-1">Tổng hóa đơn</p>
                            <p class="text-lg font-bold">{{ number_format($project->total_invoice) }}đ</p>
                        </div>
                        <div class="text-right">
                            <p class="text-blue-100 text-[10px] font-bold uppercase tracking-wider mb-1">Đã thanh toán</p>
                            <p class="text-lg font-bold text-emerald-300">{{ number_format($project->total_paid) }}đ</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Tabs -->
            <div class="px-4 mb-4">
                <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl">
                    <button 
                        @click="activeTab = 'invoices'" 
                        class="flex-1 py-3 text-sm font-bold rounded-xl transition-all"
                        :class="activeTab === 'invoices' ? 'bg-white dark:bg-slate-900 text-primary shadow-sm' : 'text-slate-500'"
                    >Hóa đơn ({{ $invoices->total() }})</button>
                    <button 
                        @click="activeTab = 'payments'" 
                        class="flex-1 py-3 text-sm font-bold rounded-xl transition-all"
                        :class="activeTab === 'payments' ? 'bg-white dark:bg-slate-900 text-primary shadow-sm' : 'text-slate-500'"
                    >Thanh toán ({{ $payments->count() }})</button>
                </div>
            </div>

            <!-- Invoices Tab Content -->
            <div x-show="activeTab === 'invoices'" class="px-4 space-y-3 pb-20">
                @forelse($invoices as $invoice)
                <div 
                    class="relative overflow-hidden rounded-2xl shadow-sm bg-slate-100 dark:bg-slate-800"
                    x-data="{ 
                        swipeX: 0, 
                        startX: 0, 
                        maxSwipe: -240, 
                        threshold: -60,
                        isSwiping: false,
                        handleTouchStart(e) {
                            this.startX = e.touches[0].clientX;
                            this.isSwiping = true;
                        },
                        handleTouchMove(e) {
                            let diff = e.touches[0].clientX - this.startX;
                            this.swipeX = Math.min(0, Math.max(this.maxSwipe, diff));
                        },
                        handleTouchEnd() {
                            this.isSwiping = false;
                            if (this.swipeX < this.threshold) {
                                this.swipeX = this.maxSwipe;
                            } else {
                                this.swipeX = 0;
                            }
                        }
                    }"
                >
                    <!-- Actions Menu (Behind) -->
                    <div class="absolute inset-0 flex justify-end h-full">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="w-[60px] h-full flex flex-col items-center justify-center bg-primary text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">visibility</span>
                            XEM
                        </a>
                        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="w-[60px] h-full flex flex-col items-center justify-center bg-violet-600 text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">picture_as_pdf</span>
                            PDF
                        </a>
                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="w-[60px] h-full flex flex-col items-center justify-center bg-amber-500 text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">edit</span>
                            SỬA
                        </a>
                        <button 
                            @click="if(confirm('Xác nhận xóa hóa đơn này?')){ $refs.deleteInvoice{{ $invoice->id }}.submit(); }"
                            class="w-[60px] h-full flex flex-col items-center justify-center bg-red-600 text-white text-[10px] font-bold"
                        >
                            <span class="material-symbols-outlined mb-1">delete</span>
                            XÓA
                            <form x-ref="deleteInvoice{{ $invoice->id }}" action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </button>
                    </div>

                    <!-- Invoice Card (Foreground) -->
                    <div 
                        class="relative bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 transition-transform"
                        :class="isSwiping ? 'duration-0' : 'duration-300'"
                        :style="`transform: translateX(${swipeX}px)`"
                        @touchstart="handleTouchStart($event)"
                        @touchmove="handleTouchMove($event)"
                        @touchend="handleTouchEnd()"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black text-slate-400 tracking-widest uppercase">{{ $invoice->code ?? '#' . $invoice->id }}</span>
                            <span class="text-[10px] font-bold text-slate-500">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold truncate text-slate-900 dark:text-white">{{ $invoice->note ?? 'Hóa đơn bán hàng' }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-base font-black text-primary">{{ number_format($invoice->total_amount) }}đ</p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 opacity-50">
                    <span class="material-symbols-outlined text-4xl mb-2">receipt_long</span>
                    <p class="text-sm font-medium">Không có hóa đơn nào.</p>
                </div>
                @endforelse
                <div class="pb-4">
                    {{ $invoices->links() }}
                </div>
            </div>

            <!-- Payments Tab Content -->
            <div x-show="activeTab === 'payments'" class="px-4 space-y-3 pb-20">
                @forelse($payments as $payment)
                <div 
                    class="relative overflow-hidden rounded-2xl shadow-sm bg-slate-100 dark:bg-slate-800"
                    x-data="{ 
                        swipeX: 0, 
                        startX: 0, 
                        maxSwipe: -120, 
                        threshold: -40,
                        isSwiping: false,
                        handleTouchStart(e) {
                            this.startX = e.touches[0].clientX;
                            this.isSwiping = true;
                        },
                        handleTouchMove(e) {
                            let diff = e.touches[0].clientX - this.startX;
                            this.swipeX = Math.min(0, Math.max(this.maxSwipe, diff));
                        },
                        handleTouchEnd() {
                            this.isSwiping = false;
                            if (this.swipeX < this.threshold) {
                                this.swipeX = this.maxSwipe;
                            } else {
                                this.swipeX = 0;
                            }
                        }
                    }"
                >
                    <!-- Actions Menu (Behind) -->
                    <div class="absolute inset-0 flex justify-end h-full">
                        <button @click="openEditPayment({{ json_encode($payment) }})" class="w-[60px] h-full flex flex-col items-center justify-center bg-indigo-500 text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">edit</span>
                            SỬA
                        </button>
                        <button 
                            @click="if(confirm('Xóa phiếu thanh toán này?')){ $refs.deletePayment{{ $payment->id }}.submit(); }"
                            class="w-[60px] h-full flex flex-col items-center justify-center bg-red-600 text-white text-[10px] font-bold"
                        >
                            <span class="material-symbols-outlined mb-1">delete</span>
                            XÓA
                            <form x-ref="deletePayment{{ $payment->id }}" action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </button>
                    </div>

                    <!-- Payment Card (Foreground) -->
                    <div 
                        class="relative bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 transition-transform"
                        :class="isSwiping ? 'duration-0' : 'duration-300'"
                        :style="`transform: translateX(${swipeX}px)`"
                        @touchstart="handleTouchStart($event)"
                        @touchmove="handleTouchMove($event)"
                        @touchend="handleTouchEnd()"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black text-emerald-500 tracking-widest uppercase">THANH TOÁN</span>
                            <span class="text-[10px] font-bold text-slate-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold truncate text-slate-900 dark:text-white">{{ $payment->note ?? 'Thanh toán thủ công' }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-base font-black text-emerald-600">+{{ number_format($payment->amount) }}đ</p>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 opacity-50">
                    <span class="material-symbols-outlined text-4xl mb-2">payments</span>
                    <p class="text-sm font-medium">Chưa có giao dịch thanh toán.</p>
                </div>
                @endforelse
            </div>
        </main>

        <!-- Floating Action Button Container -->
        <div class="fixed bottom-24 right-6 flex flex-col gap-3 z-40">
            <!-- Add Payment FAB -->
            <button @click="openAddPayment()" class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lg active:scale-90 transition-transform border-4 border-white dark:border-slate-900">
                <span class="material-symbols-outlined text-2xl">add_card</span>
            </button>
            <!-- Add Invoice FAB -->
            <a href="{{ route('projects.invoices.create', $project->id) }}" @click="isLoading = true" class="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/40 active:scale-90 transition-transform">
                <span class="material-symbols-outlined text-3xl">add_notes</span>
            </a>
        </div>

        <!-- Payment Modal -->
        <div x-cloak x-show="paymentModalOpen" class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
            <div 
                @click.away="paymentModalOpen = false"
                class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-t-3xl sm:rounded-2xl shadow-2xl p-6 overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
            >
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold" x-text="editMode ? 'Chỉnh sửa thanh toán' : 'Thêm thanh toán mới'"></h3>
                    <button @click="paymentModalOpen = false" class="text-slate-400">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editMode ? '/payments/' + paymentId : '{{ route('projects.payments.store', $project->id) }}'" method="POST" class="space-y-4" @submit="isLoading = true">
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ngày thanh toán</label>
                        <input type="date" name="payment_date" x-model="paymentData.date" required class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Số tiền (VNĐ)</label>
                        <input type="number" name="amount" x-model="paymentData.amount" required placeholder="Nhập số tiền" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ghi chú</label>
                        <textarea name="note" x-model="paymentData.note" rows="2" placeholder="Nhập ghi chú" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm"></textarea>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" @click="paymentModalOpen = false" class="flex-1 py-3 text-sm font-bold text-slate-600">Hủy</button>
                        <button type="submit" class="flex-1 bg-emerald-500 py-3 text-sm font-bold text-white rounded-xl shadow-lg shadow-emerald-500/20" x-text="editMode ? 'Cập nhật' : 'Lưu giao dịch'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
