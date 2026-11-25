@extends('layouts.dashboard')

@section('title', __('customer.title'))
@section('page_heading', __('customer.title'))

@section('content')
<div
    x-data="{
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
    class="flex flex-col gap-4 pb-20"
>
    <h1 class="text-3xl font-black tracking-tight text-text-light dark:text-text-dark">{{ __('customer.title') }}</h1>

    <x-customer.search-filter />

    <div class="space-y-4">
        @forelse($customers as $customer)
            {{-- Thao tác hiển thị sẵn (trước đây phải vuốt trái mới thấy, dễ vướng khi cuộn dọc). --}}
            <article data-customer-card="{{ $customer->id }}" class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <a href="{{ route('customers.projects.index', $customer->id) }}" class="block p-4 active:bg-gray-50 dark:active:bg-gray-800/60">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">#{{ $customer->id }}</span>
                        <span class="inline-flex items-center rounded-full {{ $customer->status == 'inactive' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600' : 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600' }} px-2.5 py-0.5 text-xs font-bold">
                            {{ $customer->status == 'inactive' ? __('customer.status.inactive') : __('customer.status.active') }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                        <div class="min-w-[min(100%,9rem)] flex-1">
                            <p class="text-base font-bold text-gray-900 [overflow-wrap:anywhere] dark:text-white">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500 [overflow-wrap:anywhere] dark:text-gray-400">{{ $customer->phone }}</p>
                        </div>
                        <div class="max-w-full text-right">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Công nợ</p>
                            <p class="text-lg font-bold tabular-nums [overflow-wrap:anywhere] {{ $customer->accountBalance() > 0 ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">{{ number_format(max(0, $customer->accountBalance()), 0, ',', '.') }} ₫</p>
                        </div>
                    </div>
                </a>

                <div class="grid grid-cols-3 border-t border-border-light text-sm font-semibold dark:border-border-dark">
                    <a href="{{ route('customers.projects.index', $customer->id) }}" class="flex min-h-[44px] items-center justify-center gap-1.5 text-primary active:bg-primary/10">
                        <span class="material-symbols-outlined text-lg">folder_open</span>
                        Dự án ({{ $customer->projects_count }})
                    </a>
                    <button type="button" @click="openEdit({{ json_encode($customer) }})" class="flex min-h-[44px] items-center justify-center gap-1.5 border-x border-border-light text-gray-700 active:bg-gray-100 dark:border-border-dark dark:text-gray-300 dark:active:bg-gray-800">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Sửa
                    </button>
                    <button type="button" @click="openDelete('{{ route('customers.destroy', $customer->id) }}')" class="flex min-h-[44px] items-center justify-center gap-1.5 text-red-600 active:bg-red-50 dark:active:bg-red-900/20">
                        <span class="material-symbols-outlined text-lg">delete</span>
                        Xóa
                    </button>
                </div>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-border-light p-8 text-center text-sm text-gray-500 dark:border-border-dark">{{ __('customer.empty') }}</p>
        @endforelse
    </div>

    <div class="px-1">
        {{ $customers->withQueryString()->onEachSide(1)->links('components.pagination-links', ['navigationLabel' => 'Phân trang khách hàng']) }}
    </div>

    {{-- Nút thêm khách: không còn thanh điều hướng dưới nên đặt sát đáy (chừa vùng an toàn của iPhone). --}}
    <button type="button" @click="openAdd()" aria-label="Thêm khách hàng"
            class="fixed right-5 bottom-[calc(1.25rem+env(safe-area-inset-bottom))] z-30 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/40 transition-transform active:scale-90">
        <span class="material-symbols-outlined text-3xl">person_add</span>
    </button>

    <!-- Modal thêm / sửa khách hàng -->
    <div
        x-cloak
        x-show="modalOpen"
        class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div
            @click.away="modalOpen = false"
            class="bg-container-light dark:bg-container-dark w-full max-w-lg rounded-t-2xl sm:rounded-xl shadow-2xl p-6 pb-[calc(1.5rem+env(safe-area-inset-bottom))] overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:scale-100"
            x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-text-light dark:text-text-dark" x-text="isEditing ? 'Cập nhật khách hàng' : 'Thêm khách hàng mới'"></h3>
                <button type="button" @click="modalOpen = false" aria-label="Đóng" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4" @submit="isLoading = true">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tên khách hàng <span class="text-red-500">*</span></label>
                    <input x-model="formData.name" name="name" type="text" required placeholder="Nhập tên khách hàng" class="w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-3 text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Địa chỉ <span class="text-red-500">*</span></label>
                    <input x-model="formData.address" name="address" type="text" required placeholder="Nhập địa chỉ" class="w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-3 text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Số điện thoại</label>
                    <input x-model="formData.phone" name="phone" type="tel" placeholder="Nhập số điện thoại" class="w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-3 text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input x-model="formData.email" name="email" type="email" placeholder="Nhập email" class="w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark p-3 text-sm focus:border-primary focus:ring-primary">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="modalOpen = false" class="flex-1 min-h-[44px] rounded-lg border border-border-light dark:border-border-dark text-sm font-bold text-gray-600 dark:text-gray-400">Hủy</button>
                    <button type="submit" class="flex-1 min-h-[44px] rounded-lg bg-primary text-sm font-bold text-white shadow-lg shadow-primary/20" x-text="isEditing ? 'Cập nhật' : 'Lưu khách hàng'"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
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
            class="bg-container-light dark:bg-container-dark w-full max-w-sm rounded-xl shadow-2xl p-6 text-center"
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
            <h3 class="text-xl font-bold mb-2 text-text-light dark:text-text-dark">Xác nhận xóa?</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa khách hàng này?</p>

            <form :action="deleteUrl" method="POST" class="flex gap-3" @submit="isLoading = true">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="flex-1 min-h-[44px] rounded-lg border border-border-light dark:border-border-dark text-sm font-bold text-gray-600 dark:text-gray-400">Hủy</button>
                <button type="submit" class="flex-1 min-h-[44px] rounded-lg bg-red-600 text-sm font-bold text-white shadow-lg shadow-red-600/20">Xóa ngay</button>
            </form>
        </div>
    </div>
</div>
@endsection
