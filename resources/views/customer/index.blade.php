@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6" x-data="{ modalOpen: false }">
        <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Customers</h1>
        <button @click="$dispatch('open-add-customer')" class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>
            <span class="truncate">Add New Customer</span>
        </button>
    </div>

    <!-- Search & Filter -->
    <x-customer.search-filter />

    <!-- Table -->
    <x-customer.table :customers="$customers" />

    <!-- Add Customer Modal -->
    <div x-data="{ open: false }" @open-add-customer.window="open = true">
        <x-modal name="open" title="Add New Customer">
            <form @submit.prevent="open = false; $dispatch('notify', { message: 'Customer added successfully!' });" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="customerName">
                        Customer Name <span class="text-red-500">*</span>
                    </label>
                    <input class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="customerName" name="customerName" placeholder="Enter customer name" required="" type="text"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="phoneNumber">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="phoneNumber" name="phoneNumber" pattern="[0-9]{9,15}" placeholder="Enter phone number" required="" type="tel"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="emailAddress">Email Address</label>
                    <input class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="emailAddress" name="emailAddress" placeholder="Enter email address" type="email"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="address">Address</label>
                    <input class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="address" name="address" placeholder="Enter address or city" type="text"/>
                </div>
                <div class="flex justify-end gap-4 pt-4 mt-2">
                    <button @click="open = false" class="rounded-lg h-10 px-4 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-bold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors" type="button">
                        Cancel
                    </button>
                    <button class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors" type="submit">
                        Save Customer
                    </button>
                </div>
            </form>
        </x-modal>
    </div>
@endsection