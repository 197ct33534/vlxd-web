@extends('layouts.dashboard')

@section('title', 'Dự án – '.$customer->name)
@section('page_heading', 'Dự án')

@section('content')
<div x-data="projectManager()" class="flex flex-col gap-4 pb-20">
    {{-- Quay lại + thông tin khách (topbar dashboard không có nút back). --}}
    <div>
        <a href="{{ route('customers.index') }}" class="-ml-1 inline-flex min-h-[44px] items-center gap-1 text-sm font-semibold text-gray-500 hover:text-primary dark:text-gray-400">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
            Quay lại khách hàng
        </a>
        <p class="text-xs font-bold uppercase tracking-widest text-primary">Khách hàng</p>
        <h1 class="text-3xl font-black tracking-tight text-text-light dark:text-text-dark">{{ $customer->name }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Đang có {{ $projects->count() }} dự án</p>
    </div>

    <div class="space-y-4">
        @forelse($projects as $project)
            {{-- Thao tác hiển thị sẵn, giống thẻ khách hàng (bỏ vuốt trái). --}}
            <article data-project-card="{{ $project->id }}" class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <a href="{{ route('projects.invoices.index', $project->id) }}" class="block p-4 active:bg-gray-50 dark:active:bg-gray-800/60">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="line-clamp-2 text-base font-bold text-gray-900 dark:text-white">{{ $project->name }}</h2>
                            <p class="mt-1 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-base">location_on</span>
                                <span class="truncate">{{ $project->address ?: 'Chưa có địa chỉ' }}</span>
                            </p>
                        </div>
                        @if($project->end_date)
                            <span class="shrink-0 rounded-lg bg-primary/10 px-2.5 py-1 text-[11px] font-bold text-primary">
                                {{ \Illuminate\Support\Carbon::parse($project->end_date)->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <dl class="mt-3 grid grid-cols-3 gap-2 rounded-lg bg-background-light p-3 text-center dark:bg-background-dark">
                        <div>
                            <dt class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">Tổng HĐ</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($project->total_invoice ?? 0) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">Đã thu</dt>
                            <dd class="text-sm font-bold text-green-600">{{ number_format($project->total_paid ?? 0) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">Còn nợ</dt>
                            <dd class="text-sm font-bold text-red-600">{{ number_format($project->total_debt ?? 0) }}</dd>
                        </div>
                    </dl>
                </a>

                <div class="grid grid-cols-3 border-t border-border-light text-sm font-semibold dark:border-border-dark">
                    <a href="{{ route('projects.invoices.index', $project->id) }}" class="flex min-h-[44px] items-center justify-center gap-1.5 text-primary active:bg-primary/10">
                        <span class="material-symbols-outlined text-lg">receipt_long</span>
                        Hóa đơn
                    </a>
                    <button type="button" @click="openEdit({{ json_encode($project) }})" class="flex min-h-[44px] items-center justify-center gap-1.5 border-x border-border-light text-gray-700 active:bg-gray-100 dark:border-border-dark dark:text-gray-300 dark:active:bg-gray-800">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Sửa
                    </button>
                    <button type="button" @click="confirmDelete('{{ route('customers.projects.destroy', ['customer' => $customer->id, 'project' => $project->id]) }}')" class="flex min-h-[44px] items-center justify-center gap-1.5 text-red-600 active:bg-red-50 dark:active:bg-red-900/20">
                        <span class="material-symbols-outlined text-lg">delete</span>
                        Xóa
                    </button>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-border-light py-12 text-center dark:border-border-dark">
                <span class="material-symbols-outlined text-4xl text-gray-400">folder_open</span>
                <p class="mt-2 font-medium text-gray-500">Chưa có dự án nào</p>
                <button type="button" @click="openAdd()" class="mt-3 min-h-[44px] text-sm font-bold text-primary">Thêm dự án đầu tiên</button>
            </div>
        @endforelse
    </div>

    <button type="button" @click="openAdd()" aria-label="Thêm dự án"
            class="fixed right-5 bottom-[calc(1.25rem+env(safe-area-inset-bottom))] z-30 flex h-14 w-14 items-center justify-center rounded-full bg-primary text-white shadow-lg shadow-primary/40 transition-transform active:scale-90">
        <span class="material-symbols-outlined text-3xl">add</span>
    </button>

    <!-- Modal thêm / sửa dự án -->
    <div x-cloak x-show="modalOpen" class="fixed inset-0 z-[60] flex items-end justify-center bg-black/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="w-full max-w-lg rounded-t-2xl bg-container-light p-6 pb-[calc(1.5rem+env(safe-area-inset-bottom))] shadow-2xl dark:bg-container-dark"
            @click.away="modalOpen = false"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
        >
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h3 class="text-xl font-bold text-text-light dark:text-text-dark" x-text="isEditing ? 'Sửa dự án' : 'Dự án mới'"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Cho khách hàng: {{ $customer->name }}</p>
                </div>
                <button type="button" @click="modalOpen = false" aria-label="Đóng" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="formAction" method="POST" @submit="isLoading = true" class="space-y-4">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tên dự án <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="formData.name" required placeholder="Nhập tên dự án"
                        class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Địa chỉ</label>
                    <input type="text" name="address" x-model="formData.address" placeholder="Địa chỉ dự án"
                        class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ngày kết thúc (dự kiến)</label>
                    <input type="date" name="end_date" x-model="formData.end_date"
                        class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="modalOpen = false" class="min-h-[44px] flex-1 rounded-lg border border-border-light text-sm font-bold text-gray-600 dark:border-border-dark dark:text-gray-400">Hủy</button>
                    <button type="submit" class="min-h-[44px] flex-1 rounded-lg bg-primary text-sm font-bold text-white shadow-lg shadow-primary/20">Lưu dự án</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-sm rounded-xl bg-container-light p-6 text-center shadow-2xl dark:bg-container-dark" @click.away="deleteModalOpen = false">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100 text-red-600 dark:bg-red-900/20">
                <span class="material-symbols-outlined text-3xl">delete_forever</span>
            </div>
            <h3 class="mb-2 text-xl font-bold text-text-light dark:text-text-dark">Xóa dự án?</h3>
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Hành động này không thể hoàn tác. Tất cả hóa đơn liên quan cũng sẽ bị ảnh hưởng.</p>

            <form :action="deleteAction" method="POST" @submit="isLoading = true" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteModalOpen = false" class="min-h-[44px] flex-1 rounded-lg border border-border-light text-sm font-bold text-gray-600 dark:border-border-dark dark:text-gray-400">Bỏ qua</button>
                <button type="submit" class="min-h-[44px] flex-1 rounded-lg bg-red-600 text-sm font-bold text-white shadow-lg shadow-red-600/20">Xóa dự án</button>
            </form>
        </div>
    </div>
</div>

<script>
    function projectManager() {
        return {
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
@endsection
