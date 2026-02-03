@extends('layouts.dashboard')

@section('title', 'Invoices - ' . $project->name)

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.projects.index', $project->customer_id) }}" class="text-gray-500 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">
                        Invoices
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Project: <span class="font-semibold text-primary">{{ $project->name }}</span></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('projects.price_history', $project->id) }}" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold shadow-lg shadow-purple-500/30">
                    <span class="material-symbols-outlined text-lg">trending_up</span>
                    Price History
                </a>
                <a href="{{ route('projects.invoices.create', $project->id) }}" class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold shadow-lg shadow-blue-500/30">
                    <span class="material-symbols-outlined text-lg">add_circle</span>
                    Create Invoice
                </a>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                        <tr>
                            <th class="px-6 py-4 font-bold">Date</th>
                            <th class="px-6 py-4 font-bold">Code</th>
                            <th class="px-6 py-4 font-bold text-center">Total Amount</th>
                            <th class="px-6 py-4 font-bold">Note</th>
                            <th class="px-6 py-4 font-bold text-right">Actions</th>
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
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openInvoiceModal({{ $invoice->id }})" 
                                           class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                           title="View Details">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                        </button>
                                        
                                        <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="inline-block" 
                                              onsubmit="return confirm('Mark this invoice as fully paid? This will create a payment record of {{ number_format($invoice->total_amount, 0, ',', '.') }} đ');">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 text-gray-500 hover:text-green-600 rounded-lg hover:bg-green-50 transition-colors"
                                                    title="Mark as Paid (100%)">
                                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                            </button>
                                        </form>

                                        <a href="{{ route('invoices.edit', $invoice->id) }}" 
                                           class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                           title="Edit Invoice">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="inline-block" 
                                              onsubmit="return confirm('Delete this invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="Delete Invoice">
                                                <span class="material-symbols-outlined text-lg">delete</span>
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
                                        <p>No invoices found for this project.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                    <h2 class="text-xl font-bold text-text-light dark:text-text-dark">Payment History</h2>
                    <button @click="openAddModal()" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold">
                        <span class="material-symbols-outlined text-lg">add_card</span>
                        Add Payment
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                            <tr>
                                <th class="px-6 py-4 font-bold">Date</th>
                                <th class="px-6 py-4 font-bold">Note</th>
                                <th class="px-6 py-4 font-bold text-right">Amount</th>
                                <th class="px-6 py-4 font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4">{{ $payment->note ?? 'Manual Payment' }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-green-600">
                                        {{ number_format($payment->amount, 0, ',', '.') }} đ
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openEditModal({{ $payment }})" 
                                                    class="p-2 text-gray-500 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                                                    title="Edit Payment">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </button>
                                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('Delete this payment? This will increase the debt.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-gray-500 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors"
                                                        title="Delete Payment">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No payments recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($payments->isNotEmpty())
                        <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-right">Total Paid:</td>
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
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Project Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Total Invoiced</span>
                        <span class="font-bold text-blue-600 text-lg">{{ number_format($project->total_invoice, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-3">
                        <span class="text-gray-500 dark:text-gray-400">Total Paid</span>
                        <span class="font-bold text-green-600 text-lg">{{ number_format($project->total_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-3">
                        <span class="text-gray-500 dark:text-gray-400">Remaining Debt</span>
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
                                <span x-text="editMode ? 'Edit Payment' : 'Add Payment'"></span>
                            </h3>
                            <form :action="formAction" method="POST" class="mt-4 space-y-4">
                                @csrf
                                <template x-if="editMode">
                                    <input type="hidden" name="_method" value="PUT">
                                </template>
                                
                                <div>
                                    <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                    <input type="date" name="payment_date" id="payment_date" required x-model="date"
                                           class="mt-1 flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (VNĐ)</label>
                                    <input type="number" name="amount" id="amount" required min="0" step="1" x-model="amount"
                                           class="mt-1 flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                           placeholder="Enter amount">
                                </div>
                                <div>
                                    <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                                    <textarea name="note" id="note" rows="2" x-model="note"
                                              class="mt-1 flex-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm"
                                              placeholder="Optional note"></textarea>
                                </div>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm">
                                        <span x-text="editMode ? 'Update' : 'Save'"></span>
                                    </button>
                                    <button type="button" @click="paymentModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Invoice Detail Modal -->
    <div x-data="{ open: false, content: '', loading: false }"
         @open-invoice-modal.window="
            open = true; 
            loading = true; 
            content = '';
            fetch('/invoices/' + $event.detail.id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => { content = html; loading = false; })
                .catch(() => { content = 'Error loading invoice.'; loading = false; });
         "
         class="relative z-50" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true"
         x-show="open" 
         style="display: none;">
        
        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                    
                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="flex justify-end absolute top-4 right-4 z-10">
                            <button @click="open = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                        
                        <div x-show="loading" class="flex justify-center py-12">
                            <span class="material-symbols-outlined animate-spin text-4xl text-primary">progress_activity</span>
                        </div>
                        
                        <div x-show="!loading" x-html="content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openInvoiceModal(id) {
            window.dispatchEvent(new CustomEvent('open-invoice-modal', { detail: { id: id } }));
        }
    </script>
@endsection
