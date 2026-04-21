@props(['customers'])

<div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                <tr>
                    <th class="px-6 py-4 font-bold cursor-pointer hover:bg-primary/10 transition-colors" scope="col">{{ __('customer.table.name') }} <span class="material-symbols-outlined text-base align-middle">unfold_more</span></th>
                    <th class="px-6 py-4 font-bold" scope="col">{{ __('customer.table.phone') }}</th>
                    <th class="px-6 py-4 font-bold" scope="col">{{ __('customer.table.email') }}</th>
                    <th class="px-6 py-4 font-bold" scope="col">{{ __('customer.table.address') }}</th>
                    <th class="px-6 py-4 font-bold text-center" scope="col">{{ __('customer.table.projects') }}</th>
                    <th class="px-6 py-4 font-bold text-center" scope="col">{{ __('customer.table.debt') }}</th>
                    <th class="px-6 py-4 font-bold text-center" scope="col">{{ __('customer.table.status') }}</th>
                    <th class="px-6 py-4 font-bold text-right" scope="col">{{ __('customer.table.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <x-customer.row :customer="$customer" />
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">{{ __('customer.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-border-light dark:border-border-dark">
        <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            {{ __('customer.pagination', [
                'from' => $customers->firstItem() ?? 0,
                'to' => $customers->lastItem() ?? 0,
                'total' => $customers->total(),
            ]) }}
        </div>
        {{ $customers->withQueryString()->links() }}
    </div>
</div>
