@extends('layouts.dashboard')

@section('title', 'Hóa đơn – '.$project->name)
@section('page_heading', 'Hóa đơn')

@section('content')
<div
    x-data="{
        activeTab: 'invoices',
        paymentModalOpen: false,
        editMode: false,
        paymentId: null,
        paymentData: { date: '{{ date('Y-m-d') }}', amount: '', note: '', method: 'cash', reference: '', invoiceId: '' },

        openAddPayment() {
            this.editMode = false;
            this.paymentId = null;
            this.paymentData = { date: '{{ date('Y-m-d') }}', amount: '', note: '', method: 'cash', reference: '', invoiceId: '' };
            this.paymentModalOpen = true;
        },
        openEditPayment(payment) {
            this.editMode = true;
            this.paymentId = payment.id;
            this.paymentData = {
                date: payment.payment_date.split(' ')[0],
                amount: payment.amount,
                note: payment.note || '',
                method: payment.payment_method || 'cash',
                reference: payment.reference_code ?? '',
                invoiceId: payment.payment_allocations[0]?.invoice_id ?? ''
            };
            this.paymentModalOpen = true;
        }
    }"
    class="flex flex-col gap-4 pb-36"
>
    {{-- Quay lại + tên công trình (topbar dashboard không có nút back). --}}
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <a href="{{ route('customers.projects.index', $project->customer_id) }}" class="-ml-1 inline-flex min-h-[44px] items-center gap-1 text-sm font-semibold text-gray-500 hover:text-primary dark:text-gray-400">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
                Quay lại dự án
            </a>
            <p class="text-xs font-bold uppercase tracking-widest text-primary">Hóa đơn công trình</p>
            <h1 class="text-2xl font-black tracking-tight text-text-light dark:text-text-dark">{{ $project->name }}</h1>
        </div>
        <a href="{{ route('projects.price_history', $project->id) }}" class="mt-11 inline-flex min-h-[44px] shrink-0 items-center gap-1 rounded-lg px-2 text-sm font-semibold text-primary hover:bg-primary/10">
            <span class="material-symbols-outlined text-lg">trending_up</span>
            {{ __('invoices.price_history') }}
        </a>
    </div>

    {{-- Tổng quan tài chính công trình --}}
    <section class="rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Tổng dư nợ</p>
        <p class="mt-1 text-3xl font-black text-red-600">{{ number_format($project->total_debt) }}đ</p>
        <dl class="mt-3 grid grid-cols-2 gap-2 rounded-lg bg-background-light p-3 text-sm dark:bg-background-dark">
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Tổng hóa đơn</dt>
                <dd class="font-bold text-gray-900 dark:text-white">{{ number_format($project->total_invoice) }}đ</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 dark:text-gray-400">Đã thanh toán</dt>
                <dd class="font-bold text-green-600">{{ number_format($project->total_paid) }}đ</dd>
            </div>
        </dl>
    </section>

    {{-- Tab hóa đơn / thanh toán --}}
    <div class="flex rounded-lg bg-gray-100 p-1 dark:bg-gray-800">
        <button type="button" @click="activeTab = 'invoices'"
            class="min-h-[44px] flex-1 rounded-md text-sm font-bold transition-all"
            :class="activeTab === 'invoices' ? 'bg-container-light text-primary shadow-sm dark:bg-container-dark' : 'text-gray-500'"
        >Hóa đơn ({{ $invoices->total() }})</button>
        <button type="button" @click="activeTab = 'payments'"
            class="min-h-[44px] flex-1 rounded-md text-sm font-bold transition-all"
            :class="activeTab === 'payments' ? 'bg-container-light text-primary shadow-sm dark:bg-container-dark' : 'text-gray-500'"
        >Thanh toán ({{ $payments->count() }})</button>
    </div>

    {{-- Danh sách hóa đơn: thao tác hiển thị sẵn (bỏ vuốt trái). --}}
    <div x-show="activeTab === 'invoices'" class="space-y-3">
        @forelse($invoices as $invoice)
            <article data-invoice-card="{{ $invoice->id }}" class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <a href="{{ route('invoices.show', $invoice->id) }}" class="block p-4 active:bg-gray-50 dark:active:bg-gray-800/60">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">{{ $invoice->code ?? '#' . $invoice->id }}</span>
                        <span class="text-xs font-semibold text-gray-500">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <p class="min-w-0 flex-1 truncate text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->note ?? 'Hóa đơn bán hàng' }}</p>
                        <p class="shrink-0 text-base font-black text-primary">{{ number_format($invoice->total_amount) }}đ</p>
                    </div>
                </a>

                <div class="grid grid-cols-3 border-t border-border-light text-sm font-semibold dark:border-border-dark">
                    <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" class="flex min-h-[44px] items-center justify-center gap-1.5 text-primary active:bg-primary/10">
                        <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                        PDF
                    </a>
                    <a href="{{ route('invoices.edit', $invoice->id) }}" class="flex min-h-[44px] items-center justify-center gap-1.5 border-x border-border-light text-gray-700 active:bg-gray-100 dark:border-border-dark dark:text-gray-300 dark:active:bg-gray-800">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Sửa
                    </a>
                    <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST"
                          @submit="if (! confirm('Xác nhận xóa hóa đơn này?')) { $event.preventDefault(); return; } isLoading = true">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex min-h-[44px] w-full items-center justify-center gap-1.5 text-red-600 active:bg-red-50 dark:active:bg-red-900/20">
                            <span class="material-symbols-outlined text-lg">delete</span>
                            Xóa
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-border-light py-10 text-center text-gray-500 dark:border-border-dark">
                <span class="material-symbols-outlined text-4xl">receipt_long</span>
                <p class="mt-1 text-sm font-medium">Không có hóa đơn nào.</p>
            </div>
        @endforelse
        <div>{{ $invoices->links() }}</div>
    </div>

    {{-- Danh sách thanh toán --}}
    <div x-show="activeTab === 'payments'" x-cloak class="space-y-3">
        @forelse($payments as $payment)
            <article data-payment-card="{{ $payment->id }}" class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <div class="p-4">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-green-600">Thanh toán</span>
                        <span class="text-xs font-semibold text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <p class="min-w-0 flex-1 truncate text-sm font-bold text-gray-900 dark:text-white">{{ $payment->note ?? 'Thanh toán thủ công' }}</p>
                        <p class="shrink-0 text-base font-black text-green-600">+{{ number_format($payment->amount) }}đ</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 border-t border-border-light text-sm font-semibold dark:border-border-dark">
                    <button type="button" @click="openEditPayment({{ json_encode($payment) }})" class="flex min-h-[44px] items-center justify-center gap-1.5 border-r border-border-light text-gray-700 active:bg-gray-100 dark:border-border-dark dark:text-gray-300 dark:active:bg-gray-800">
                        <span class="material-symbols-outlined text-lg">edit</span>
                        Sửa
                    </button>
                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST"
                          @submit="if (! confirm('Xóa phiếu thanh toán này?')) { $event.preventDefault(); return; } isLoading = true">
                        @csrf @method('DELETE')
                        <button type="submit" class="flex min-h-[44px] w-full items-center justify-center gap-1.5 text-red-600 active:bg-red-50 dark:active:bg-red-900/20">
                            <span class="material-symbols-outlined text-lg">delete</span>
                            Xóa
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-border-light py-10 text-center text-gray-500 dark:border-border-dark">
                <span class="material-symbols-outlined text-4xl">payments</span>
                <p class="mt-1 text-sm font-medium">Chưa có giao dịch thanh toán.</p>
            </div>
        @endforelse
    </div>

    {{-- Nút nổi: thêm thanh toán / tạo hóa đơn --}}
    <div class="fixed right-5 bottom-[calc(1.25rem+env(safe-area-inset-bottom))] z-30 flex flex-col items-end gap-3">
        <button type="button" @click="openAddPayment()" class="flex min-h-[44px] items-center gap-1.5 rounded-full bg-green-600 px-4 text-sm font-bold text-white shadow-lg shadow-green-600/30 transition-transform active:scale-95">
            <span class="material-symbols-outlined text-xl">add_card</span>
            {{ __('invoices.add_payment') }}
        </button>
        <a href="{{ route('projects.invoices.create', $project->id) }}" class="flex min-h-[52px] items-center gap-1.5 rounded-full bg-primary px-5 text-sm font-bold text-white shadow-lg shadow-primary/40 transition-transform active:scale-95">
            <span class="material-symbols-outlined text-2xl">add_notes</span>
            {{ __('invoices.create') }}
        </a>
    </div>

    {{-- Modal thanh toán --}}
    <div x-cloak x-show="paymentModalOpen" class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm">
        <div
            @click.away="paymentModalOpen = false"
            class="max-h-[92dvh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-container-light p-6 pb-[calc(1.5rem+env(safe-area-inset-bottom))] shadow-2xl sm:rounded-xl dark:bg-container-dark"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
        >
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-bold text-text-light dark:text-text-dark" x-text="editMode ? 'Chỉnh sửa thanh toán' : 'Thêm thanh toán mới'"></h3>
                <button type="button" @click="paymentModalOpen = false" aria-label="{{ __('common.close') }}" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="editMode ? '/payments/' + paymentId : '{{ route('projects.payments.store', $project->id) }}'" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="isLoading = true">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ngày thanh toán</label>
                    <input type="date" name="payment_date" x-model="paymentData.date" required class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Số tiền (VNĐ)</label>
                    <input type="number" name="amount" x-model="paymentData.amount" required placeholder="Nhập số tiền" inputmode="numeric" class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Hình thức</label>
                    <select name="payment_method" x-model="paymentData.method" class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                        <option value="cash">Tiền mặt</option>
                        <option value="bank_transfer">Chuyển khoản</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Mã giao dịch</label>
                    <input type="text" name="reference_code" x-model="paymentData.reference" class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ảnh bill</label>
                    <input type="file" name="proof_image" accept="image/*" class="w-full text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phân bổ</label>
                    <select name="invoice_id" x-model="paymentData.invoiceId" class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                        <option value="">Tự động trừ hóa đơn cũ nhất (FIFO)</option>
                        @foreach($project->invoices()->orderBy('invoice_date')->get() as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->code ?: ('#'.$inv->id) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ghi chú</label>
                    <textarea name="note" x-model="paymentData.note" rows="2" placeholder="Nhập ghi chú" class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark"></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="paymentModalOpen = false" class="min-h-[44px] flex-1 rounded-lg border border-border-light text-sm font-bold text-gray-600 dark:border-border-dark dark:text-gray-400">Hủy</button>
                    <button type="submit" class="min-h-[44px] flex-1 rounded-lg bg-green-600 text-sm font-bold text-white shadow-lg shadow-green-600/20" x-text="editMode ? 'Cập nhật' : 'Lưu giao dịch'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
