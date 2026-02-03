<aside class="w-64 flex-shrink-0 bg-container-light dark:bg-container-dark border-r border-border-light dark:border-border-dark flex flex-col justify-between p-4 shadow-subtle">
    <div class="flex flex-col gap-8">
        <div class="flex items-center gap-3 px-3">
            <div class="bg-primary text-white rounded-lg p-2">
                <span class="material-symbols-outlined text-2xl">
                    business_center
                </span>
            </div>
            <h1 class="text-text-light dark:text-text-dark text-xl font-bold">CMS Inc.</h1>
        </div>
        <nav class="flex flex-col gap-2">
            <x-nav-link href="#" icon="dashboard">Dashboard</x-nav-link>
            <x-nav-link href="{{ route('customers.index') }}" icon="group" :active="request()->routeIs('customers.*')">Customers</x-nav-link>
            <x-nav-link href="{{ route('employees.index') }}" icon="badge" :active="request()->routeIs('employees.*')">Employees</x-nav-link>
            <x-nav-link href="{{ route('daily-reports.index') }}" icon="assignment" :active="request()->routeIs('daily-reports.*')">Daily Reports</x-nav-link>
            <x-nav-link href="#" icon="folder">Projects</x-nav-link>
            <x-nav-link href="#" icon="receipt_long">Invoices</x-nav-link>
            <x-nav-link href="#" icon="bar_chart">Reports</x-nav-link>
            <x-nav-link href="#" icon="settings">Settings</x-nav-link>
        </nav>
    </div>
    <div class="flex flex-col gap-2">
        <x-nav-link href="#" icon="help">Help</x-nav-link>
        <x-nav-link href="#" icon="logout">Logout</x-nav-link>
    </div>
</aside>
