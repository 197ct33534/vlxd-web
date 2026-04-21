@props(['customers'])

<div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                <tr>
                    <th class="px-6 py-4 font-bold cursor-pointer hover:bg-primary/10 transition-colors" scope="col">Customer Name <span class="material-symbols-outlined text-base align-middle">unfold_more</span></th>
                    <th class="px-6 py-4 font-bold" scope="col">Phone</th>
                    <th class="px-6 py-4 font-bold" scope="col">Email</th>
                    <th class="px-6 py-4 font-bold" scope="col">Address</th>
                    <th class="px-6 py-4 font-bold text-center" scope="col">Total Projects</th>
                    <th class="px-6 py-4 font-bold text-center" scope="col">Total Debt</th>
                    <th class="px-6 py-4 font-bold text-center" scope="col">Status</th>
                    <th class="px-6 py-4 font-bold text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <x-customer.row :customer="$customer" />
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-border-light dark:border-border-dark">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            Hiển thị <span class="font-semibold text-gray-900 dark:text-white">{{ $customers->firstItem() ?? 0 }}</span>
            –
            <span class="font-semibold text-gray-900 dark:text-white">{{ $customers->lastItem() ?? 0 }}</span>
            trong tổng <span class="font-semibold">{{ $customers->total() }}</span> khách hàng
        </div>
        {{ $customers->withQueryString()->links() }}
    </div>
</div>
