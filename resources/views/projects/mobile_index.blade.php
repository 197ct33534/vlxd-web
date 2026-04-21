<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Danh sách dự án - {{ $customer->name }}</title>
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
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-display bg-background-light dark:bg-background-dark min-h-screen text-gray-800 dark:text-gray-200"
    x-data="projectManager()"
    x-init="isLoading = false"
>
    <!-- Loading Indicator -->
    <div x-cloak x-show="isLoading" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-2xl flex flex-col items-center gap-4">
            <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-slate-700 dark:text-slate-200 font-medium text-sm">Đang tải...</span>
        </div>
    </div>

    <!-- Header -->
    <header class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark px-4 py-3 sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('customers.index') }}" @click="isLoading = true" class="inline-flex max-w-[45%] items-center gap-1.5 -ml-2 rounded-lg p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                <span class="material-symbols-outlined shrink-0 text-2xl">arrow_back</span>
                <span class="truncate text-xs font-semibold">{{ __('nav.back_short') }}</span>
            </a>
            <h1 class="text-gray-900 dark:text-white text-lg font-bold">Dự án</h1>
        </div>
        <div class="flex items-center gap-2">
            <div class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                {{ substr($customer->name, 0, 1) }}
            </div>
        </div>
    </header>

    <main class="flex-1 p-4 pb-24">
        <!-- Customer Info -->
        <div class="mb-6">
            <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Khách hàng</p>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">{{ $customer->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Đang có {{ $projects->count() }} dự án</p>
        </div>

        <!-- Project List -->
        <div class="space-y-4">
            @forelse($projects as $project)
                <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700"
                    x-data="{ 
                        startX: 0, 
                        currentX: 0, 
                        isSwiped: false,
                        swipeThreshold: 100,
                        maxSwipe: -200,
                        openSwipe() { this.currentX = this.maxSwipe; this.isSwiped = true; },
                        closeSwipe() { this.currentX = 0; this.isSwiped = false; },
                        handleTouchStart(e) { this.startX = e.touches[0].clientX; },
                        handleTouchMove(e) {
                            let diff = e.touches[0].clientX - this.startX;
                            if (diff < 0) this.currentX = Math.max(diff, this.maxSwipe);
                            if (diff > 0 && this.isSwiped) this.currentX = Math.min(this.maxSwipe + diff, 0);
                        },
                        handleTouchEnd() {
                            if (this.currentX < -this.swipeThreshold) this.openSwipe();
                            else this.closeSwipe();
                        }
                    }"
                    @touchstart="handleTouchStart"
                    @touchmove="handleTouchMove"
                    @touchend="handleTouchEnd"
                >
                    <!-- Swipe Actions -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1 bg-red-50 dark:bg-gray-900">
                        <a href="{{ route('projects.invoices.index', $project->id) }}" @click="isLoading = true" class="w-14 h-14 flex flex-col items-center justify-center text-blue-500">
                            <span class="material-symbols-outlined text-xl">receipt_long</span>
                            <span class="text-[8px] font-bold uppercase mt-1">Hóa đơn</span>
                        </a>
                        <button @click="openEdit({{ json_encode($project) }})" class="w-14 h-14 flex flex-col items-center justify-center text-amber-500">
                            <span class="material-symbols-outlined text-xl">edit</span>
                            <span class="text-[8px] font-bold uppercase mt-1">Sửa</span>
                        </button>
                        <button @click="confirmDelete('{{ route('customers.projects.destroy', ['customer' => $customer->id, 'project' => $project->id]) }}')" class="w-14 h-14 flex flex-col items-center justify-center text-red-500">
                            <span class="material-symbols-outlined text-xl">delete</span>
                            <span class="text-[8px] font-bold uppercase mt-1">Xóa</span>
                        </button>
                    </div>

                    <!-- Content Card -->
                    <div class="relative transition-transform duration-200 bg-white dark:bg-gray-800 p-4"
                        :style="`transform: translateX(${currentX}px)`"
                        @click="if(isSwiped) closeSwipe()"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1 pr-4">
                                <h3 class="font-bold text-gray-900 dark:text-white text-lg line-clamp-1">{{ $project->name }}</h3>
                                <div class="flex items-center gap-1.5 mt-1 text-gray-500 dark:text-gray-400">
                                    <span class="material-symbols-outlined text-base">location_on</span>
                                    <span class="text-xs">{{ $project->address ?? 'Chưa có địa chỉ' }}</span>
                                </div>
                            </div>
                            @if($project->end_date)
                                <div class="shrink-0 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase">
                                    {{ date('M d, Y', strtotime($project->end_date)) }}
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50">
                            <div class="text-center">
                                <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider mb-0.5">Tổng đơn</p>
                                <p class="text-xs font-black text-gray-800 dark:text-gray-200">{{ number_format($project->total_invoice ?? 0) }}</p>
                            </div>
                            <div class="text-center border-x border-gray-50 dark:border-gray-700/50">
                                <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider mb-0.5">Đã thu</p>
                                <p class="text-xs font-black text-green-500">{{ number_format($project->total_paid ?? 0) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-[9px] uppercase font-bold text-gray-400 tracking-wider mb-0.5">Còn nợ</p>
                                <p class="text-xs font-black text-red-500">{{ number_format($project->total_debt ?? 0) }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between items-center">
                            <a href="{{ route('projects.invoices.index', $project->id) }}" @click="isLoading = true" class="text-primary text-[11px] font-black flex items-center gap-1 uppercase tracking-wider">
                                Xem hóa đơn <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                            <div class="flex -space-x-2">
                                <div class="w-6 h-6 rounded-full border-2 border-white dark:border-gray-800 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[10px] text-gray-400">receipt_long</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl text-gray-400">folder_open</span>
                    </div>
                    <p class="text-gray-500 font-medium">Chưa có dự án nào</p>
                    <button @click="openAdd()" class="mt-4 text-primary font-bold text-sm">Thêm dự án đầu tiên</button>
                </div>
            @endforelse
        </div>
    </main>

    <!-- FAB -->
    <button @click="openAdd()" class="fixed bottom-6 right-6 w-14 h-14 bg-primary text-white rounded-full shadow-2xl shadow-primary/40 flex items-center justify-center hover:scale-110 active:scale-95 transition-transform z-40">
        <span class="material-symbols-outlined text-3xl">add</span>
    </button>

    <!-- Add/Edit Modal (Bottom Sheet) -->
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-t-[2.5rem] p-6 pb-12 shadow-2xl relative"
            @click.away="modalOpen = false"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
        >
            <div class="w-12 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-6"></div>
            
            <header class="mb-6">
                <h2 class="text-xl font-black text-gray-900 dark:text-white" x-text="isEditing ? 'Sửa dự án' : 'Dự án mới'"></h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Cho khách hàng: {{ $customer->name }}</p>
            </header>

            <form :action="formAction" method="POST" @submit="isLoading = true">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tên dự án <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="formData.name" required
                            class="w-full h-14 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all dark:text-white"
                            placeholder="Nhập tên dự án...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Địa chỉ</label>
                        <input type="text" name="address" x-model="formData.address"
                            class="w-full h-14 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all dark:text-white"
                            placeholder="Địa chỉ dự án...">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Ngày kết thúc (Dự kiến)</label>
                        <input type="date" name="end_date" x-model="formData.end_date"
                            class="w-full h-14 bg-gray-50 dark:bg-gray-800 border-0 rounded-2xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-8">
                    <button type="button" @click="modalOpen = false" 
                        class="h-14 rounded-2xl text-sm font-black text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 transition-colors">
                        HỦY
                    </button>
                    <button type="submit" 
                        class="h-14 rounded-2xl text-sm font-black text-white bg-primary shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all">
                        LƯU DỰ ÁN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-6">
        <div class="bg-white dark:bg-slate-900 w-full max-w-sm rounded-[2rem] p-8 shadow-2xl overflow-hidden" @click.away="deleteModalOpen = false">
            <div class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-3xl text-red-500">delete_forever</span>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white text-center mb-2">Xóa dự án?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-8">Hành động này không thể hoàn tác. Tất cả hóa đơn liên quan cũng sẽ bị ảnh hưởng.</p>
            
            <div class="flex flex-col gap-3">
                <form :action="deleteAction" method="POST" @submit="isLoading = true" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-4 text-sm font-black text-white bg-red-500 rounded-2xl shadow-lg shadow-red-500/30">XÓA DỰ ÁN</button>
                </form>
                <button @click="deleteModalOpen = false" class="w-full py-4 text-sm font-black text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-2xl">BỎ QUA</button>
            </div>
        </div>
    </div>

    <script>
        function projectManager() {
            return {
                isLoading: false,
                modalOpen: false,
                isEditing: false,
                formAction: '',
                formData: { name: '', address: '', end_date: '' },
                deleteModalOpen: false,
                deleteAction: '',

                openAdd() {
                    this.isEditing = false;
                    this.formAction = '{{ route('customers.projects.store', $customer->id) }}';
                    this.formData = { name: '', address: '', end_date: '' };
                    this.modalOpen = true;
                },

                openEdit(project) {
                    this.isEditing = true;
                    this.formAction = `/customers/{{ $customer->id }}/projects/${project.id}`;
                    this.formData = { 
                        name: project.name, 
                        address: project.address || '', 
                        end_date: project.end_date || '' 
                    };
                    this.modalOpen = true;
                },

                confirmDelete(action) {
                    this.deleteAction = action;
                    this.deleteModalOpen = true;
                }
            }
        }
    </script>
</body>
</html>
