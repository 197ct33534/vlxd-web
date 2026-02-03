    <div 
        class="fixed bottom-5 right-5 z-50" 
        x-cloak 
        x-data="{ toastVisible: {{ session('success') ? 'true' : 'false' }}, toastMessage: '{{ session('success') }}' }"
        x-init="if(toastVisible) setTimeout(() => toastVisible = false, 3000); $watch('toastVisible', value => { if(value) setTimeout(() => toastVisible = false, 3000) })"
        @notify.window="toastMessage = $event.detail.message; toastVisible = true"
        x-show="toastVisible" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-end="transform opacity-100 translate-y-0" 
        x-transition:enter-start="transform opacity-0 translate-y-2" 
        x-transition:leave="transition ease-in duration-300" 
        x-transition:leave-end="transform opacity-0 translate-y-2" 
        x-transition:leave-start="transform opacity-100 translate-y-0"
    >
        <div class="flex items-center gap-3 bg-green-500 text-white py-2 px-4 rounded-lg shadow-lg">
            <span class="material-symbols-outlined">check_circle</span>
            <p class="text-sm font-medium" x-text="toastMessage"></p>
        </div>
    </div>
