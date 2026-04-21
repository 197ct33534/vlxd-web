@extends('layouts.dashboard')

@section('title', __('invoices.index.page_title', ['name' => $project->name]))

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.projects.index', $project->customer_id) }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-primary transition-colors text-sm font-semibold">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span>{{ __('nav.back_short') }}</span>
                </a>
                <div>
                    <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">
                        {{ __('invoices.title_suffix') }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('invoices.index.project_caption') }} <span class="font-semibold text-primary">{{ $project->name }}</span></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('projects.price_history', $project->id) }}" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold shadow-lg shadow-purple-500/30">
                    <span class="material-symbols-outlined text-lg">trending_up</span>
                    {{ __('invoices.price_history') }}
                </a>
                <a href="{{ route('projects.invoices.create', $project->id) }}" class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold shadow-lg shadow-blue-500/30">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    {{ __('invoices.create') }}
                </a>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                        <tr>
                            <th class="px-6 py-4 font-bold">{{ __('invoices.table.date') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('invoices.table.code') }}</th>
                            <th class="px-6 py-4 font-bold text-center">{{ __('invoices.table.total_amount') }}</th>
                            <th class="px-6 py-4 font-bold">{{ __('invoices.table.note') }}</th>
                            <th class="px-6 py-4 font-bold text-right">{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">{{ $invoice->code ?? '#' . $invoice->id }}</td>
                                <td class="px-6 py-4 text-center font-bold text-blue-600">
                                    {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                                </td>
                                <td class="px-6 py-4 truncate max-w-xs">{{ $invoice->note ?? '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-wrap items-center justify-end gap-1.5">
                                        <button type="button" @click="openInvoiceModal({{ $invoice->id }})"
                                           class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-blue-900/20"
                                           title="{{ __('invoices.view_details') }}">
                                            <span class="material-symbols-outlined text-base leading-none">visibility</span>
                                            <span>{{ __('invoices.btn_detail') }}</span>
                                        </button>
                                        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-violet-50 hover:text-violet-700 dark:text-gray-400 dark:hover:bg-violet-900/20">
                                            <span class="material-symbols-outlined text-base leading-none">picture_as_pdf</span>
                                            <span>{{ __('invoices.btn_pdf') }}</span>
                                        </a>
                                        <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="inline-flex"
                                              onsubmit="return confirm(@js(__('invoices.confirm_mark_paid', ['amount' => number_format($invoice->total_amount, 0, ',', '.') . ' đ'])));">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-green-50 hover:text-green-700 dark:text-gray-400 dark:hover:bg-green-900/20">
                                                <span class="material-symbols-outlined text-base leading-none">check_circle</span>
                                                <span>{{ __('invoices.btn_mark_paid') }}</span>
                                            </button>
                                        </form>
                                        <a href="{{ route('invoices.edit', $invoice->id) }}"
                                           class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400">
                                            <span class="material-symbols-outlined text-base leading-none">edit</span>
                                            <span>{{ __('common.edit') }}</span>
                                        </a>
                                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-flex"
                                              onsubmit="return confirm(@js(__('invoices.confirm_delete')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-red-50 hover:text-red-600 dark:text-gray-400">
                                                <span class="material-symbols-outlined text-base leading-none">delete</span>
                                                <span>{{ __('common.delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-4xl text-gray-300">receipt_long</span>
                                        <p>{{ __('invoices.empty_project') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>

        <div class="px-4 py-3 border-t border-border-light dark:border-border-dark">
            {{ $invoices->links() }}
        </div>

        <!-- Payment History & Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
             x-data="{ 
                paymentModalOpen: false, 
                editMode: false,
                paymentId: null,
                date: '{{ date('Y-m-d') }}',
                amount: '',
                note: '',
                projectId: {{ $project->id }},
                
                openAddModal() {
                    this.editMode = false;
                    this.paymentId = null;
                    this.date = '{{ date('Y-m-d') }}';
                    this.amount = '';
                    this.note = '';
                    this.paymentModalOpen = true;
                },

                openEditModal(payment) {
                    this.editMode = true;
                    this.paymentId = payment.id;
                    // Format date to YYYY-MM-DD
                    this.date = payment.payment_date.split(' ')[0]; 
                    this.amount = payment.amount;
                    this.note = payment.note || '';
                    this.paymentModalOpen = true;
                },

                get formAction() {
                    if (this.editMode) {
                        return '/payments/' + this.paymentId;
                    }
                    return '/projects/' + this.projectId + '/payments';
                }
             }">
            
            <!-- Payment History List -->
            <div class="lg:col-span-2 bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
                <div class="p-6 border-b border-border-light dark:border-border-dark flex justify-between items-center">
                    <h2 class="text-xl font-bold text-text-light dark:text-text-dark">{{ __('invoices.payment_history') }}</h2>
                    <button @click="openAddModal()" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold">
                        <span class="material-symbols-outlined text-lg">add_card</span>
                        {{ __('invoices.add_payment') }}
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                            <tr>
                                <th class="px-6 py-4 font-bold">{{ __('invoices.table.date') }}</th>
                                <th class="px-6 py-4 font-bold">{{ __('invoices.table.note') }}</th>
                                <th class="px-6 py-4 font-bold text-right">{{ __('common.amount') }}</th>
                                <th class="px-6 py-4 font-bold text-right">{{ __('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $payment->note ?? __('invoices.payment_note_default') }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        {{ number_format($payment->amount, 0, ',', '.') }} đ
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex flex-wrap items-center justify-end gap-1.5">
                                            <button type="button" @click="openEditModal({{ $payment }})"
                                                    class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400">
                                                <span class="material-symbols-outlined text-base leading-none">edit</span>
                                                <span>{{ __('common.edit') }}</span>
                                            </button>
                                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline-flex"
                                                  onsubmit="return confirm(@js(__('invoices.confirm_delete_payment')));">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-red-50 hover:text-red-600 dark:text-gray-400">
                                                    <span class="material-symbols-outlined text-base leading-none">delete</span>
                                                    <span>{{ __('common.delete') }}</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        {{ __('invoices.payments_empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($payments->isNotEmpty())
                        <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right">{{ __('invoices.total_paid_footer') }}</td>
                                <td class="px-6 py-4 text-right text-green-600">{{ number_format($project->total_paid, 0, ',', '.') }} đ</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Project Summary (Optional sidebar or small card) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-subtle p-6 h-fit">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('invoices.summary.title') }}</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('invoices.summary.total_invoiced') }}</span>
                        <span class="font-bold text-blue-600 text-lg">{{ number_format($project->total_invoice, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('invoices.summary.total_paid') }}</span>
                        <span class="font-bold text-green-600 text-lg">{{ number_format($project->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-3">
                        <span class="text-gray-500 dark:text-gray-400">{{ __('invoices.summary.remaining_debt') }}</span>
                        <span class="font-bold text-red-600 text-xl">{{ number_format($project->total_debt, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Modal -->
            <div x-show="paymentModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="paymentModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="paymentModalOpen = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="paymentModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                <span x-text="editMode ? @js(__('invoices.payment_modal.edit')) : @js(__('invoices.payment_modal.add'))"></span>
                            </h3>
                            <form :action="formAction" method="POST" class="mt-4 space-y-4">
                                @csrf
                                <template x-if="editMode">
                                    <input type="hidden" name="_method" value="PUT">
                                </template>
                                
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('common.date') }}</label>
                                    <input type="date" name="payment_date" id="payment_date" required x-model="date"
                                           class="mt-1 flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('common.amount') }} (VNĐ)</label>
                                    <input type="number" name="amount" id="amount" required min="0" step="1" x-model="amount"
                                           class="mt-1 flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                           placeholder="{{ __('invoices.payment_modal.amount_placeholder') }}">
                                </div>
                                <div>
                                    <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('common.note') }}</label>
                                    <textarea name="note" id="note" rows="2" x-model="note"
                                              class="mt-1 flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                              placeholder="{{ __('invoices.payment_modal.note_placeholder') }}"></textarea>
                                </div>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                                        <span class="material-symbols-outlined text-xl">save</span>
                                        <span x-text="editMode ? @js(__('invoices.payment_modal.update')) : @js(__('common.save'))"></span>
                                    </button>
                                    <button type="button" @click="paymentModalOpen = false" class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                        <span class="material-symbols-outlined text-xl">close</span>
                                        <span>{{ __('common.cancel') }}</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal xem nhanh hóa đơn (fetch trong script) -->
    <div x-data="invoiceDetailModal()"
         @open-invoice-modal.window="fetchInvoice($event.detail.id)"
         class="relative z-50"
         aria-labelledby="invoice-modal-title"
         role="dialog"
         aria-modal="true"
         x-show="open"
         style="display: none;">

        <div class="fixed inset-0 z-50 flex items-end justify-center overflow-y-auto p-0 sm:items-center sm:p-4">
            <div x-show="open"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-gray-900/60 backdrop-blur-[1px]"
                 @click="open = false"></div>

            <div x-show="open"
                 @click.stop
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 flex max-h-[min(92vh,900px)] w-full max-w-4xl flex-col overflow-hidden rounded-t-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-900 sm:mx-4 sm:rounded-2xl">

                {{-- Thanh tiêu đề popup --}}
                <div id="invoice-modal-title" class="flex shrink-0 items-center justify-between gap-3 border-b border-gray-200 bg-gradient-to-r from-primary/15 via-white to-slate-50 px-4 py-3 dark:border-gray-700 dark:from-primary/25 dark:via-gray-900 dark:to-gray-900 sm:px-5">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="material-symbols-outlined shrink-0 text-2xl text-primary">receipt_long</span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-primary sm:text-xs">{{ __('invoices.modal.preview_title') }}</p>
                            <p class="truncate text-base font-bold text-gray-900 dark:text-white">{{ __('invoices.detail.heading') }}</p>
                        </div>
                    </div>
                    <button type="button" @click="open = false" class="inline-flex shrink-0 items-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                        <span class="material-symbols-outlined text-xl">close</span>
                        <span class="hidden sm:inline">{{ __('common.close') }}</span>
                    </button>
                </div>

                {{-- Nội dung cuộn --}}
                <div class="min-h-0 flex-1 overflow-y-auto bg-slate-50/80 dark:bg-gray-950/50">
                    <div x-show="loading" class="flex flex-col items-center justify-center gap-3 py-16">
                        <span class="material-symbols-outlined animate-spin text-4xl text-primary">progress_activity</span>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('invoices.modal.loading') }}</p>
                    </div>
                    <div x-show="!loading" x-html="content" class="p-3 sm:p-5"></div>
                </div>

                {{-- Chân: link trang đầy đủ --}}
                <div x-show="!loading && invoiceId" class="shrink-0 border-t border-gray-200 bg-white px-4 py-3 text-center dark:border-gray-700 dark:bg-gray-900">
                    <a :href="'{{ url('/invoices') }}/' + invoiceId" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline">
                        <span class="material-symbols-outlined text-lg">open_in_new</span>
                        {{ __('invoices.open_full_page') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openInvoiceModal(id) {
            window.dispatchEvent(new CustomEvent('open-invoice-modal', { detail: { id: id } }));
        }

        function invoiceDetailModal() {
            const invoiceBaseUrl = @json(url('/invoices'));
            const invoiceLoadError = @json(__('invoices.error_load_invoice'));

            return {
                open: false,
                content: '',
                loading: false,
                invoiceId: null,
                async fetchInvoice(id) {
                    this.open = true;
                    this.loading = true;
                    this.content = '';
                    this.invoiceId = id;
                    try {
                        const res = await fetch(invoiceBaseUrl + '/' + id + '?modal=1', {
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'text/html',
                            },
                        });
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }
                        this.content = await res.text();
                    } catch (e) {
                        this.content = '<p class="p-4 text-red-600">' + invoiceLoadError + '</p>';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
@endsection
