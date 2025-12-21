@props(['customer'])

@php
    $statusColor = $customer->status === 'active' ? 'green' : 'red';
@endphp

<tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
    <td class="px-4 sm:px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
    <td class="px-4 sm:px-6 py-4 hidden sm:table-cell">{{ $customer->phone ?? '-' }}</td>
    <td class="px-4 sm:px-6 py-4 hidden md:table-cell">{{ $customer->email }}</td>
    <td class="px-4 sm:px-6 py-4 hidden lg:table-cell">{{ $customer->company ?? '-' }}</td>
    <td class="px-4 sm:px-6 py-4 text-center hidden md:table-cell">{{ $customer->projects_count ?? 0 }}</td>
    <td class="px-4 sm:px-6 py-4 text-center">
        <x-ui.badge color="{{ $statusColor }}">{{ ucfirst($customer->status) }}</x-ui.badge>
    </td>
    <td class="px-4 sm:px-6 py-4 text-right space-x-1">
        <button class="p-1 text-gray-500 hover:text-primary rounded-full"><span class="material-symbols-outlined text-xl">visibility</span></button>
        <button class="p-1 text-gray-500 hover:text-primary rounded-full"><span class="material-symbols-outlined text-xl">edit</span></button>
        <button class="p-1 text-gray-500 hover:text-red-500 rounded-full"><span class="material-symbols-outlined text-xl">delete</span></button>
    </td>
</tr>
