<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ __('customer.title') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
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
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
        {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style type="text/tailwindcss">
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased" 
    x-data="{ 
        isLoading: false, 
        modalOpen: false, 
        isEditing: false,
        formAction: '{{ route('customers.store') }}',
        formData: { name: '', phone: '', email: '', address: '' },
        deleteModalOpen: false,
        deleteUrl: '',
        openAdd() {
            this.isEditing = false;
            this.modalOpen = true;
            this.formAction = '{{ route('customers.store') }}';
            this.formData = { name: '', phone: '', email: '', address: '' };
        },
        openEdit(customer) {
            this.isEditing = true;
            this.modalOpen = true;
            this.formAction = '/customers/update/' + customer.id;
            this.formData = { name: customer.name, phone: customer.phone, email: customer.email, address: customer.address };
        },
        openDelete(url) {
            this.deleteUrl = url;
            this.deleteModalOpen = true;
        }
    }"
    @click="
        const link = $event.target.closest('a');
        if (link && link.href && !link.href.includes('#') && !link.getAttribute('href').startsWith('javascript:') && !link.hasAttribute('download') && link.target !== '_blank') {
            isLoading = true;
        }
    ">

    <!-- Loading Indicator -->
    <div 
        x-cloak 
        x-show="isLoading" 
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-end="opacity-100"
        x-transition:enter-start="opacity-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-end="opacity-0"
        x-transition:leave-start="opacity-100"
    >
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-4">
            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-slate-700 dark:text-slate-200 font-medium text-sm">{{ __('common.processing') }}</span>
        </div>
    </div>

    <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 shadow-sm">
        <div class="flex items-center gap-3">
            <button type="button" class="inline-flex h-10 max-w-[9rem] items-center gap-1.5 rounded-lg bg-slate-100 px-2 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                <span class="material-symbols-outlined shrink-0">menu</span>
                <span class="truncate text-xs font-semibold">{{ __('nav.open_menu') }}</span>
            </button>
            <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-white">
                    <span class="material-symbols-outlined text-sm">group</span>
                </div>
                <h1 class="text-lg font-bold tracking-tight">{{ __('nav.admin_default') }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" class="relative inline-flex h-10 max-w-[7.5rem] items-center gap-1 rounded-lg px-1.5 text-slate-600 dark:text-slate-400">
                <span class="material-symbols-outlined shrink-0">notifications</span>
                <span class="truncate text-xs font-semibold">{{ __('nav.notifications') }}</span>
                <span class="absolute right-1 top-1.5 flex h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-900"></span>
            </button>
            <div class="h-8 w-8 overflow-hidden rounded-full border border-slate-200 dark:border-slate-700">
                <img class="h-full w-full object-cover" data-alt="User profile avatar icon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcc7nhnE7oEV_l0fgZrcrl2rwDNQk7W_RbWyXoeaWzY0mW5XVhdijOFQbeMDaI3CTf2cHOE6k3dCmdrf7Rp2t4YRGlPYFdvnQWBtVuuBUtaRTHmzgKK8dC2lQMX2IomxOu74KAujxKhj4qPvZFWbbY0AL24n92kZpUu5y3trNRPq6-5xLfMLYEnD3l9yJL7M9DNlzHrFzEg7VGFcblZGl330rkY6fkbsFOyXfJKpOWJWRRyj8pWhuyvvpHyDtJqD5LK-KkprHPhcw"/>
            </div>
        </div>
    </header>
    <main class="mx-auto max-w-lg p-4 pb-28">
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-4">{{ __('customer.title') }}</h2>
            <form action="{{ route('customers.index') }}" method="GET" class="relative" @submit="isLoading = true">
                @if(request()->has('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                <input name="search" value="{{ request('search') }}" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 pl-10 pr-4 py-3 text-sm focus:border-primary focus:ring-primary shadow-sm outline-none" placeholder="{{ __('customer.filter.search_placeholder') }}" type="search" aria-label="{{ __('common.search') }}"/>
            </form>
        </div>
        
        <div class="flex gap-2 overflow-x-auto pb-4 mb-2 custom-scrollbar">
            <a href="{{ route('customers.index', array_filter(['search' => request('search')])) }}" class="whitespace-nowrap rounded-full {{ request('status', 'all') == 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }} px-4 py-1.5 text-xs font-semibold shadow-sm">{{ __('customer.filter.all') }}</a>
            <a href="{{ route('customers.index', array_filter(['search' => request('search'), 'status' => 'active'])) }}" class="whitespace-nowrap rounded-full {{ request('status') == 'active' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }} px-4 py-1.5 text-xs font-semibold">{{ __('customer.filter.active') }}</a>
            <a href="{{ route('customers.index', array_filter(['search' => request('search'), 'status' => 'inactive'])) }}" class="whitespace-nowrap rounded-full {{ request('status') == 'inactive' ? 'bg-primary text-white' : 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400' }} px-4 py-1.5 text-xs font-semibold">{{ __('customer.filter.inactive') }}</a>
        </div>

        <div class="space-y-4">
            @forelse($customers as $customer)
                <div 
                    class="relative overflow-hidden rounded-xl shadow-sm bg-slate-100 dark:bg-slate-800"
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
                        <a href="{{ route('customers.index') }}" class="w-[60px] h-full flex flex-col items-center justify-center bg-slate-400 text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">visibility</span>
                            XEM
                        </a>
                        <a href="{{ route('customers.projects.index', $customer->id) }}" class="w-[60px] h-full flex flex-col items-center justify-center bg-indigo-500 text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">folder_open</span>
                            DỰ ÁN
                        </a>
                        <button @click="openEdit({{ json_encode($customer) }})" class="w-[60px] h-full flex flex-col items-center justify-center bg-amber-500 text-white text-[10px] font-bold font-bold">
                            <span class="material-symbols-outlined mb-1">edit</span>
                            SỬA
                        </button>
                        <button @click="openDelete('{{ route('customers.destroy', $customer->id) }}')" class="w-[60px] h-full flex flex-col items-center justify-center bg-red-600 text-white text-[10px] font-bold">
                            <span class="material-symbols-outlined mb-1">delete</span>
                            XÓA
                        </button>
                    </div>

                    <!-- Customer Card (Swipable Foreground) -->
                    <div 
                        class="relative bg-white dark:bg-slate-900 p-4 border border-slate-200 dark:border-slate-800 transition-transform cursor-grab active:cursor-grabbing"
                        :class="isSwiping ? 'duration-0' : 'duration-300'"
                        :style="`transform: translateX(${swipeX}px)`"
                        @touchstart="handleTouchStart($event)"
                        @touchmove="handleTouchMove($event)"
                        @touchend="handleTouchEnd()"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">#{{ $customer->id }}</span>
                            <span class="inline-flex items-center rounded-full {{ $customer->status == 'inactive' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' }} px-2.5 py-0.5 text-xs font-bold">
                                {{ $customer->status == 'inactive' ? __('customer.status.inactive') : __('customer.status.active') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-bold truncate">{{ $customer->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $customer->phone }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ number_format($customer->total_debt ?? 0) }}đ</p>
                                <div class="text-primary text-xs font-semibold flex items-center justify-end gap-1 mt-1">
                                    {{ $customer->projects_count }} dự án <span class="material-symbols-outlined text-[10px] text-slate-400">swipe_left</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-slate-500">{{ __('customer.empty') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6 px-1">
            {{ $customers->withQueryString()->links() }}
        </div>
    </main>

    <div class="fixed bottom-24 right-6 z-40">
        <button @click="openAdd()" class="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/40 active:scale-90 transition-transform">
            <span class="material-symbols-outlined text-3xl">person_add</span>
        </button>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 z-40 flex h-20 items-center justify-around border-t border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg px-4 pb-4">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400">
            <span class="material-symbols-outlined">home</span>
            <span class="text-[10px] font-bold">{{ __('mobile.nav.home') }}</span>
        </a>
        <a href="{{ route('customers.index') }}" class="flex flex-col items-center gap-1 text-primary">
            <span class="material-symbols-outlined">group</span>
            <span class="text-[10px] font-bold">{{ __('mobile.nav.clients') }}</span>
        </a>
        <div class="relative -top-6">
            <button class="flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/40 ring-4 ring-background-light dark:ring-background-dark">
                <span class="material-symbols-outlined text-3xl">add</span>
            </button>
        </div>
        <a href="{{ route('invoice.index') }}" class="flex flex-col items-center gap-1 text-slate-400">
            <span class="material-symbols-outlined">receipt_long</span>
            <span class="text-[10px] font-bold">{{ __('mobile.nav.invoices') }}</span>
        </a>
        <button class="flex flex-col items-center gap-1 text-slate-400">
            <span class="material-symbols-outlined">settings</span>
            <span class="text-[10px] font-bold">{{ __('mobile.nav.settings') }}</span>
        </button>
    </nav>

    <!-- Add/Edit Customer Modal -->
    <div 
        x-cloak 
        x-show="modalOpen" 
        class="fixed inset-0 z-[50] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="modalOpen = false"
            class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-t-3xl sm:rounded-2xl shadow-2xl p-6 overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:scale-100"
            x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold" x-text="isEditing ? 'Cập nhật khách hàng' : 'Thêm khách hàng mới'"></h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4" @submit="isLoading = true">
                @csrf
                <div x-show="isEditing">
                    <input type="hidden" name="_method" value="POST"> {{-- Controller uses POST for update --}}
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tên khách hàng <span class="text-red-500">*</span></label>
                    <input x-model="formData.name" name="name" type="text" required placeholder="Nhập tên khách hàng" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm focus:border-primary focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Địa chỉ <span class="text-red-500">*</span></label>
                    <input x-model="formData.address" name="address" type="text" required placeholder="Nhập địa chỉ" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm focus:border-primary focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Số điện thoại</label>
                    <input x-model="formData.phone" name="phone" type="tel" placeholder="Nhập số điện thoại" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm focus:border-primary focus:ring-primary outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                    <input x-model="formData.email" name="email" type="email" placeholder="Nhập email" class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 p-3 text-sm focus:border-primary focus:ring-primary outline-none">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" @click="modalOpen = false" class="flex-1 rounded-xl border border-slate-200 dark:border-slate-800 py-3 text-sm font-bold text-slate-600 dark:text-slate-400">Hủy</button>
                    <button type="submit" class="flex-1 rounded-xl bg-primary py-3 text-sm font-bold text-white shadow-lg shadow-primary/20" x-text="isEditing ? 'Cập nhật' : 'Lưu khách hàng'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
        x-cloak 
        x-show="deleteModalOpen" 
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="deleteModalOpen = false"
            class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="scale-95"
            x-transition:enter-end="scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="scale-100"
            x-transition:leave-end="scale-95"
        >
            <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">delete</span>
            </div>
            <h3 class="text-xl font-bold mb-2">Xác nhận xóa?</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6">Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa khách hàng này?</p>

            <form :action="deleteUrl" method="POST" class="flex gap-4" @submit="isLoading = true">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="flex-1 rounded-xl border border-slate-200 dark:border-slate-800 py-3 text-sm font-bold text-slate-600 dark:text-slate-400">Hủy</button>
                <button type="submit" class="flex-1 rounded-xl bg-red-600 py-3 text-sm font-bold text-white shadow-lg shadow-red-600/20">Xóa ngay</button>
            </form>
        </div>
    </div>
</body>
</html>