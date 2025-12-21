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
                    <th class="px-6 py-4 font-bold text-center" scope="col">Status</th>
                    <th class="px-6 py-4 font-bold text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <x-customer.row :customer="$customer" />
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination (Static for now as per index.blade.php, but can be dynamic if $customers is paginated) -->
    <div class="flex items-center justify-between p-4 border-t border-border-light dark:border-border-dark">
        <span class="text-sm text-gray-700 dark:text-gray-400">
            Showing <span class="font-semibold text-gray-900 dark:text-white">{{ $customers->count() }}</span> Entries
        </span>
        <div class="inline-flex -space-x-px rounded-md shadow-sm text-sm">
            <button class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-container-dark dark:border-border-dark dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Previous
            </button>
            <button class="flex items-center justify-center px-3 h-8 text-primary border-y border-primary bg-primary/20 dark:bg-primary/30 dark:text-white dark:border-primary">
                1
            </button>
            <button class="flex items-center justify-center px-3 h-8 text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-container-dark dark:border-border-dark dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Next
            </button>
        </div>
    </div>
</div>
