@props(['customer'])

<tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
    <td class="px-4 sm:px-6 py-4 font-medium text-gray-900 dark:text-white">
        <a href="{{ route('customers.projects.index', $customer->id) }}" class="hover:text-primary hover:underline">
            {{ $customer->name }}
        </a>
    </td>
    <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">{{ $customer->phone ?? '-' }}</td>
    <td class="px-4 sm:px-6 py-4 hidden md:table-cell">{{ $customer->email }}</td>
    <td class="px-4 sm:px-6 py-4 hidden lg:table-cell">{{ $customer->company ?? '-' }}</td>
    <td class="px-4 sm:px-6 py-4 text-center hidden md:table-cell">{{ $customer->projects_count ?? 0 }}</td>
    <td class="px-4 sm:px-6 py-4 text-center">
        @if($customer->status == 'active')
            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full dark:bg-green-900/40 dark:text-green-300">{{ __('customer.status.active') }}</span>
        @else
            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full dark:bg-gray-700 dark:text-gray-200">{{ __('customer.status.inactive') }}</span>
        @endif
    </td>
    <td class="px-4 sm:px-6 py-4 text-right">
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
