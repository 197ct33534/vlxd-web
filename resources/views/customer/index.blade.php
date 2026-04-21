@extends('layouts.dashboard')

@section('page_heading', __('customer.title'))

@section('content')
    <div 
        class="flex flex-col gap-6" 
        x-data="{ 
            modalOpen: false, 
            isEditing: false, 
            formAction: '{{ route('customers.store') }}', 
            formMethod: 'POST',
            formData: { name: '', phone: '', email: '', address: '' },
            deleteModalOpen: false,
            deleteUrl: ''
        }"
        @open-add-customer.window="
            modalOpen = true; 
            isEditing = false; 
            formAction = '{{ route('customers.store') }}'; 
            formMethod = 'POST'; 
            formData = { name: '', phone: '', email: '', address: '' }
        "
        @open-edit-customer.window="
            modalOpen = true; 
            isEditing = true; 
            let c = $event.detail.customer;
            formAction = '/customers/update/' + c.id; 
            formMethod = 'POST'; 
            formData = { name: c.name, phone: c.phone, email: c.email, address: c.address }
        "
        @open-delete-customer.window="
            deleteModalOpen = true;
            deleteUrl = $event.detail.url;
        "
    >
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">{{ __('customer.title') }}</h1>
            <button @click="$dispatch('open-add-customer')" class="flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors">
                <span class="material-symbols-outlined text-base">add</span>
                <span>{{ __('customer.add_new') }}</span>
            </button>
        </div>

        <!-- Search & Filter -->
        <x-customer.search-filter />

        <!-- Table -->
        <x-customer.table :customers="$customers" />

        <!-- Add/Edit Customer Modal -->
        <x-modal name="modalOpen">
            <x-slot:header>
                <span x-text="isEditing ? @js(__('customer.modal.edit')) : @js(__('customer.add_new'))"></span>
            </x-slot:header>
            <form :action="formAction" method="POST" class="space-y-4" @submit="isLoading = true">
                @csrf
                <!-- Method Spoofing for Edit (if needed, though controller uses POST for update) -->
                <!-- <div x-html="isEditing ? '<input type=\'hidden\' name=\'_method\' value=\'PUT\'>' : ''"></div> -->

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="customerName">
                        {{ __('customer.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input x-model="formData.name" class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="customerName" name="name" placeholder="{{ __('customer.placeholder.name') }}" required="" type="text"/>
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="address">
                        {{ __('customer.address') }} <span class="text-red-500">*</span>
                    </label>
                    <input x-model="formData.address" class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="address" name="address" placeholder="{{ __('customer.placeholder.address') }}" required="" type="text"/>
                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="phoneNumber">
                        {{ __('customer.phone') }}
                    </label>
                    <input x-model="formData.phone" class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="phoneNumber" name="phone" placeholder="{{ __('customer.placeholder.phone') }}" type="tel"/>
                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="emailAddress">{{ __('auth.email_address') }}</label>
                    <input x-model="formData.email" class="mt-1 block w-full rounded-lg border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary focus:border-primary text-text-light dark:text-text-dark placeholder:text-gray-400 dark:placeholder:text-gray-500" id="emailAddress" name="email" placeholder="{{ __('customer.placeholder.email') }}" type="email"/>
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-4 pt-4 mt-2">
                    <button @click="modalOpen = false" class="inline-flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-bold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors" type="button">
                        <span class="material-symbols-outlined text-lg">close</span>
                        {{ __('common.cancel') }}
                    </button>
                    <button class="inline-flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold shadow-subtle hover:bg-primary/90 transition-colors" type="submit">
                        <span class="material-symbols-outlined text-lg">save</span>
                        <span x-text="isEditing ? @js(__('customer.modal.update')) : @js(__('customer.modal.save'))"></span>
                    </button>
                </div>
            </form>
        </x-modal>

        <!-- Delete Confirmation Modal -->
        <x-modal name="deleteModalOpen" title="{{ __('customer.confirm_delete_title') }}" width="max-w-md">
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-300">{{ __('customer.confirm_delete_body') }}</p>
                <div class="flex justify-end gap-4">
                    <button type="button" @click="deleteModalOpen = false" class="inline-flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-bold hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                        {{ __('common.cancel') }}
                    </button>
                    <form :action="deleteUrl" method="POST" class="inline-flex" @submit="isLoading = true">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg h-10 px-4 bg-red-500 text-white text-sm font-bold shadow-subtle hover:bg-red-600 transition-colors">
                            <span class="material-symbols-outlined text-lg">delete</span>
                            {{ __('common.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
@endsection