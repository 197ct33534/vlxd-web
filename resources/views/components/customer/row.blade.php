@props(['customer'])

<tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $customer->name }}</td>
    <td class="px-6 py-4">{{ $customer->phone }}</td>
    <td class="px-6 py-4">{{ $customer->email ?? '-' }}</td>
    <td class="px-6 py-4">{{ $customer->address ?? '-' }}</td>
    <td class="px-6 py-4 text-center">{{ $customer->projects_count ?? 0 }}</td>
    <td class="px-6 py-4 text-center">
        <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Active</span>
    </td>
    <td class="px-6 py-4 text-right">
        <button class="p-1 text-gray-500 dark:text-gray-400 hover:text-primary rounded-full transition-colors"><span class="material-symbols-outlined text-xl">visibility</span></button>
        <button class="p-1 text-gray-500 dark:text-gray-400 hover:text-primary rounded-full transition-colors"><span class="material-symbols-outlined text-xl">edit</span></button>
        <button class="p-1 text-gray-500 dark:text-gray-400 hover:text-red-500 rounded-full transition-colors"><span class="material-symbols-outlined text-xl">delete</span></button>
    </td>
</tr>
