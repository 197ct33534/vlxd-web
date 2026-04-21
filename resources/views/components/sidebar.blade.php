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
            <x-nav-link href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">Tổng quan</x-nav-link>
            <x-nav-link href="{{ route('customers.index') }}" icon="group" :active="request()->routeIs('customers.index') || request()->routeIs('customers.projects.*')">Khách hàng</x-nav-link>
            <x-nav-link href="{{ route('projects.index') }}" icon="folder" :active="request()->routeIs('projects.index')">Tất cả dự án</x-nav-link>
            <x-nav-link href="{{ route('material-prices.index') }}" icon="sell" :active="request()->routeIs('material-prices.*')">Báo giá vật tư</x-nav-link>
            <x-nav-link href="{{ route('invoice.index') }}" icon="upload_file" :active="request()->routeIs('invoice.*')">Nhập hóa đơn Excel</x-nav-link>
            <x-nav-link href="{{ route('employees.index') }}" icon="badge" :active="request()->routeIs('employees.*')">Nhân viên</x-nav-link>
            <x-nav-link href="{{ route('daily-reports.index') }}" icon="assignment" :active="request()->routeIs('daily-reports.*')">Báo cáo ngày</x-nav-link>
            <x-nav-link href="{{ route('store-settings.edit') }}" icon="storefront" :active="request()->routeIs('store-settings.*')">Thông tin cửa hàng</x-nav-link>
        </nav>
    </div>
    <div class="flex flex-col gap-2">
        <x-nav-link href="{{ route('landing') }}" icon="public" :active="false">Trang chủ website</x-nav-link>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-lg text-text-light dark:text-text-dark hover:bg-red-500/10 transition-colors w-full text-left">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-sm font-medium">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>
