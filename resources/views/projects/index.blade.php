@extends('layouts.dashboard')

@section('title', 'Projects - ' . $customer->name)

@section('content')
    <div 
        class="flex flex-col gap-6"
        x-data="{ 
            modalOpen: false, 
            isEditing: false, 
            formAction: '', 
            formMethod: 'POST',
            formData: { name: '', address: '', end_date: '' },
            deleteModalOpen: false,
            deleteUrl: ''
        }"
    >
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-primary transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">
                    Projects: <span class="text-primary">{{ $customer->name }}</span>
                </h1>
            </div>
            <button 
                @click="
                    modalOpen = true; 
                    isEditing = false; 
                    formAction = '{{ route('customers.projects.store', $customer->id) }}'; 
                    formMethod = 'POST';
                    formData = { name: '', address: '', end_date: '' }
                "
                class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors"
            >
                <span class="material-symbols-outlined text-base">add</span>
                <span class="truncate">Add New Project</span>
            </button>
        </div>

        <!-- Projects Table -->
        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                        <tr>
                            <th class="px-6 py-4 font-bold">Project Name</th>
                            <th class="px-6 py-4 font-bold">Address</th>
                            <th class="px-6 py-4 font-bold">End Date</th>
                            <th class="px-6 py-4 font-bold text-center">Total Invoice</th>
                            <th class="px-6 py-4 font-bold text-center">Paid</th>
                            <th class="px-6 py-4 font-bold text-center">Debt</th>
                            <th class="px-6 py-4 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $project->name }}</td>
                                <td class="px-6 py-4">{{ $project->address ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $project->end_date ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">{{ number_format($project->total_invoice ?? 0) }}</td>
                                <td class="px-6 py-4 text-center text-green-600">{{ number_format($project->total_paid ?? 0) }}</td>
                                <td class="px-6 py-4 text-center text-red-600">{{ number_format($project->total_debt ?? 0) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button 
                                        @click="
                                            modalOpen = true; 
                                            isEditing = true; 
                                            formAction = '{{ route('customers.projects.update', ['customer' => $customer->id, 'project' => $project->id]) }}';
                                            formData = { 
                                                name: '{{ addslashes($project->name) }}', 
                                                address: '{{ addslashes($project->address ?? '') }}', 
                                                end_date: '{{ $project->end_date }}' 
                                            }
                                        "
                                        class="p-1 text-gray-500 dark:text-gray-400 hover:text-primary rounded-full transition-colors"
                                    >
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </button>
                                    <a href="{{ route('projects.invoices.index', $project->id) }}" 
                                       class="p-1 text-gray-500 dark:text-gray-400 hover:text-blue-600 rounded-full transition-colors"
                                       title="View Invoices">
                                        <span class="material-symbols-outlined text-xl">receipt_long</span>
                                    </a>
                                    <a href="{{ route('invoice.index', ['project_id' => $project->id]) }}" 
                                       class="p-1 text-gray-500 dark:text-gray-400 hover:text-green-600 rounded-full transition-colors"
                                       title="Import Invoice">
                                        <span class="material-symbols-outlined text-xl">upload_file</span>
                                    </a>
                                    <button 
                                        @click="
                                            deleteModalOpen = true; 
                                            deleteUrl = '{{ route('customers.projects.destroy', ['customer' => $customer->id, 'project' => $project->id]) }}'
                                        "
                                        class="p-1 text-gray-500 dark:text-gray-400 hover:text-red-500 rounded-full transition-colors"
                                    >
                                        <span class="material-symbols-outlined text-xl">delete</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No projects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Project Modal -->
        <x-modal name="modalOpen">
            <x-slot:header>
                <span x-text="isEditing ? 'Edit Project' : 'Add New Project'"></span>
            </x-slot:header>
            <form :action="formAction" method="POST" class="space-y-4" @submit="isLoading = true">
                @csrf
                <div>
                    <input type="hidden" name="_method" value="PUT" :disabled="!isEditing">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Project Name <span class="text-red-500">*</span>
                    </label>
                    <input x-model="formData.name" name="name" type="text" required
                           class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"
                           placeholder="Enter project name" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                    <input x-model="formData.address" name="address" type="text"
                           class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark"
                           placeholder="Enter address" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                    <input x-model="formData.end_date" name="end_date" type="date"
                           class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark" />
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <button @click="modalOpen = false" type="button" class="rounded-lg h-10 px-4 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-bold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors">
                        Save Project
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Delete Modal -->
        <x-modal name="deleteModalOpen" title="Confirm Delete" width="max-w-md">
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-300">Are you sure you want to delete this project? This action cannot be undone.</p>
                <div class="flex justify-end gap-4">
                    <button @click="deleteModalOpen = false" class="rounded-lg h-10 px-4 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-bold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        Cancel
                    </button>
                    <form :action="deleteUrl" method="POST" class="inline" @submit="isLoading = true">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg h-10 px-4 bg-red-500 text-white text-sm font-bold shadow-subtle hover:bg-red-600 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
@endsection
