@extends('layouts.dashboard')

@section('title', __('employees.page_title'))

@section('content')
<div class="flex flex-col gap-6" x-data="employeeManagement()">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">{{ __('employees.title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('employees.subtitle') }}</p>
        </div>
        <button @click="openCreateModal()" class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold shadow-lg shadow-blue-500/30">
            <span class="material-symbols-outlined text-lg">person_add</span>
            {{ __('employees.add') }}
        </button>
    </div>

    <!-- Employee Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($employees as $employee)
            <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-6 flex flex-col gap-4 relative group hover:shadow-lg transition-all">
                
                <!-- Action Dropdown -->
                <div class="absolute top-4 right-4" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <span class="material-symbols-outlined">more_vert</span>
                    </button>
                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-xl py-1 z-10 border border-gray-100 dark:border-gray-700">
                        <button @click="openEditModal({{ $employee }})" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="material-symbols-outlined text-lg mr-2">edit</span> {{ __('common.edit') }}
                        </button>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" onsubmit="return confirm(@js(__('employees.confirm_delete')));">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="material-symbols-outlined text-lg mr-2">delete</span> {{ __('common.delete') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Avatar & Info -->
                <div class="flex items-center gap-4">
                    @if($employee->avatar)
                        <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}" class="w-16 h-16 rounded-full object-cover ring-2 ring-primary/20">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xl font-bold ring-2 ring-white dark:ring-gray-700 shadow-md">
                            {{ $employee->initials }}
                        </div>
                    @endif
                    <div>
                        <h3 class="font-bold text-lg text-text-light dark:text-text-dark">{{ $employee->name }}</h3>
                        <p class="text-sm text-gray-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">phone</span>
                            {{ $employee->phone ?? __('employees.no_phone') }}
                        </p>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>

                <!-- Financial Stats -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">{{ __('employees.base_salary') }}</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ number_format($employee->base_salary, 0, ',', '.') }} đ</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">{{ __('employees.total_advanced') }}</p>
                        <p class="font-bold text-orange-500">{{ number_format($employee->salary_advances_sum_amount ?? 0, 0, ',', '.') }} đ</p>
                    </div>
                </div>

                <button @click="openAdvanceModal({{ $employee->id }}, '{{ $employee->name }}')" class="mt-2 w-full py-2 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 text-primary text-sm font-semibold rounded-lg transition-colors border border-gray-200 dark:border-gray-600">
                    {{ __('employees.manage_advances') }}
                </button>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">badge</span>
                <p class="text-lg">{{ __('employees.empty') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $employees->links() }}
    </div>

    <!-- Employee Form Modal (Create/Edit) -->
    <div x-show="isFormOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isFormOpen = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" x-text="editMode ? @js(__('employees.modal.edit')) : @js(__('employees.modal.new'))"></h3>
                
                <form :action="formAction" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('employees.field.avatar') }}</label>
                        <input type="file" name="avatar" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('employees.field.name') }}</label>
                        <input type="text" name="name" x-model="formData.name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('employees.field.phone') }}</label>
                        <input type="text" name="phone" x-model="formData.phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('employees.field.base_salary') }}</label>
                        <input type="number" name="base_salary" x-model="formData.base_salary" required min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    </div>

                    <div class="mt-5 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                            <span class="material-symbols-outlined text-xl">save</span>
                            {{ __('common.save') }}
                        </button>
                        <button type="button" @click="isFormOpen = false" class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            <span class="material-symbols-outlined text-xl">close</span>
                            {{ __('common.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Advance Management Modal -->
    <div x-show="isAdvanceOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="isAdvanceOpen = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ __('employees.advances_prefix') }} <span x-text="currentEmployeeName" class="text-primary"></span></h3>
                    <button type="button" @click="isAdvanceOpen = false" class="inline-flex items-center gap-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="material-symbols-outlined">close</span>
                        <span>{{ __('common.close') }}</span>
                    </button>
                </div>

                <!-- Add Advance Form -->
                <form :action="advanceFormAction" method="POST" class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-6 border border-gray-200 dark:border-gray-600">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('common.date') }}</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('common.amount') }}</label>
                            <input type="number" name="amount" required min="1" placeholder="{{ __('employees.advance_placeholder_amount') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        </div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:text-sm">
                            <span class="material-symbols-outlined text-xl">add_card</span>
                            {{ __('employees.advance_add_record') }}
                        </button>
                    </div>
                </form>

                <!-- History Table -->
                <div class="overflow-y-auto max-h-64">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.amount') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.note') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="advance in advances" :key="advance.id">
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="formatDate(advance.date)"></td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-bold text-orange-600" x-text="formatCurrency(advance.amount)"></td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500" x-text="advance.note || '-'"></td>
                                </tr>
                            </template>
                            <template x-if="advances.length === 0">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">{{ __('employees.advance_no_history') }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function employeeManagement() {
        return {
            isFormOpen: false,
            isAdvanceOpen: false,
            editMode: false,
            currentEmployeeId: null,
            currentEmployeeName: '',
            formData: { name: '', phone: '', base_salary: 0 },
            advances: [],

            get formAction() {
                return this.editMode ? '/employees/' + this.currentEmployeeId : '/employees';
            },

            get advanceFormAction() {
                return '/employees/' + this.currentEmployeeId + '/advances';
            },

            openCreateModal() {
                this.editMode = false;
                this.formData = { name: '', phone: '', base_salary: 0 };
                this.isFormOpen = true;
            },

            openEditModal(employee) {
                this.editMode = true;
                this.currentEmployeeId = employee.id;
                this.formData = { 
                    name: employee.name, 
                    phone: employee.phone, 
                    base_salary: employee.base_salary 
                };
                this.isFormOpen = true;
            },

            async openAdvanceModal(id, name) {
                this.currentEmployeeId = id;
                this.currentEmployeeName = name;
                this.isAdvanceOpen = true;
                this.advances = []; // specific loading state?
                
                // Fetch advances
                try {
                    let res = await fetch('/employees/' + id + '/advances');
                    this.advances = await res.json();
                } catch (e) {
                    console.error('Failed to load advances', e);
                }
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
            },
            
            formatDate(dateStr) {
                 if(!dateStr) return '';
                 return new Date(dateStr).toLocaleDateString('vi-VN');
            }
        }
    }
</script>
@endsection
