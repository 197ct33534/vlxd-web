@props(['customers'])

<div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[680px] table-fixed text-left text-sm text-gray-500 dark:text-gray-400">
            <colgroup>
                <col class="w-[21%]">
                <col class="w-[18%]">
                <col class="w-[19%]">
                <col class="w-[8%]">
                <col class="w-[13%]">
                <col class="w-[10%]">
                <col class="w-[11%]">
            </colgroup>
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                <tr>
                    <th class="px-3 py-3 font-bold" scope="col">{{ __('customer.table.name') }}</th>
                    <th class="px-3 py-3 font-bold" scope="col">Liên hệ</th>
                    <th class="px-3 py-3 font-bold" scope="col">{{ __('customer.table.address') }}</th>
                    <th class="px-2 py-3 text-center font-bold" scope="col">{{ __('customer.table.projects') }}</th>
                    <th class="px-2 py-3 text-right font-bold" scope="col">{{ __('customer.table.debt') }}</th>
                    <th class="px-2 py-3 text-center font-bold" scope="col">{{ __('customer.table.status') }}</th>
                    <th class="px-2 py-3 text-right font-bold" scope="col">{{ __('customer.table.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <x-customer.row :customer="$customer" />
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">{{ __('customer.empty') }}</td>
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
        {{ $customers->withQueryString()->onEachSide(1)->links('components.pagination-links', ['navigationLabel' => 'Phân trang khách hàng']) }}
    </div>
</div>
