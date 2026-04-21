@props(['customer'])

<tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
        <a href="{{ route('customers.projects.index', $customer->id) }}" class="hover:text-primary hover:underline">
            {{ $customer->name }}
        </a>
    </td>
    <td class="px-6 py-4">{{ $customer->phone }}</td>
    <td class="px-6 py-4">{{ $customer->email ?? '-' }}</td>
    <td class="px-6 py-4">{{ $customer->address ?? '-' }}</td>
    <td class="px-6 py-4 text-center">
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded dark:bg-blue-200 dark:text-blue-800">
            {{ $customer->projects_count ?? 0 }}
        </span>
    </td>
    <td class="px-6 py-4 text-center font-bold text-red-600">
        {{ number_format($customer->total_debt ?? 0, 0, ',', '.') }} đ
    </td>
    <td class="px-6 py-4 text-center">
        @if($customer->status == 'active')
            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900/40 dark:text-green-300">{{ __('customer.status.active') }}</span>
        @else
            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-200">{{ __('customer.status.inactive') }}</span>
        @endif
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex flex-wrap items-center justify-end gap-1.5">
            <a href="{{ route('customers.projects.index', $customer->id) }}"
               class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-600 dark:text-gray-400 dark:hover:bg-primary/10 dark:hover:text-primary">
                <span class="material-symbols-outlined text-base leading-none">folder_open</span>
                <span>{{ __('customer.view_projects') }}</span>
            </a>
            <button type="button"
                class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
                <span class="material-symbols-outlined text-base leading-none">visibility</span>
                <span>{{ __('customer.btn.preview') }}</span>
            </button>
            <button type="button"
                @click="$dispatch('open-edit-customer', { customer: {{ json_encode($customer) }} })"
                class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-primary/10 hover:text-primary dark:text-gray-400">
                <span class="material-symbols-outlined text-base leading-none">edit</span>
                <span>{{ __('common.edit') }}</span>
            </button>
            <button type="button"
                @click="$dispatch('open-delete-customer', { url: '{{ route('customers.destroy', $customer->id) }}' })"
                class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-xs font-semibold text-gray-600 hover:bg-red-50 hover:text-red-600 dark:text-gray-400 dark:hover:bg-red-900/20">
                <span class="material-symbols-outlined text-base leading-none">delete</span>
                <span>{{ __('common.delete') }}</span>
            </button>
        </div>
    </td>
</tr>
