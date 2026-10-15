<aside
    class="fixed inset-y-0 left-0 z-50 flex w-[min(18rem,88vw)] flex-shrink-0 flex-col justify-between border-r border-border-light bg-container-light p-4 shadow-xl transition-transform duration-300 ease-out dark:border-border-dark dark:bg-container-dark lg:static lg:z-auto lg:w-64 lg:max-w-none lg:shadow-subtle
           -translate-x-full transform lg:translate-x-0"
    :class="{ 'translate-x-0': $store.layout.sidebarOpen }"
    @keydown.window.escape="$store.layout.closeSidebar()"
>
    <div class="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto">
        <div class="flex items-center justify-between gap-2 px-1">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex-shrink-0 rounded-lg bg-primary p-2 text-white">
                    <span class="material-symbols-outlined text-2xl">business_center</span>
                </div>
                <h1 class="truncate text-lg font-bold text-text-light dark:text-text-dark">VLXD</h1>
            </div>
            <button
                type="button"
                class="inline-flex max-w-[9rem] items-center gap-1 rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 lg:hidden mf-touch"
                @click="$store.layout.closeSidebar()"
            >
                <span class="material-symbols-outlined shrink-0 text-2xl">close</span>
            </button>
        </div>
        <nav
            class="flex flex-col gap-1"
            @click="if ($event.target.closest('a[href]')) { $store.layout.closeSidebar(); }"
        >
            <x-nav-link href="{{ route('dashboard') }}" icon="dashboard" :active="request()->routeIs('dashboard')">Tổng quan</x-nav-link>
            <x-nav-link href="{{ route('customers.index') }}" icon="group" :active="request()->routeIs('customers.index') || request()->routeIs('customers.projects.*')">Khách hàng</x-nav-link>
            <x-nav-link href="{{ route('projects.index') }}" icon="folder" :active="request()->routeIs('projects.index')">Tất cả dự án</x-nav-link>
            <x-nav-link href="{{ route('material-prices.index') }}" icon="sell" :active="request()->routeIs('material-prices.*')">Báo giá vật tư</x-nav-link>
            <x-nav-link href="{{ route('invoice.index') }}" icon="upload_file" :active="request()->routeIs('invoice.*')">Nhập hóa đơn Excel</x-nav-link>
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.orders.index', ['status' => 'pending_confirm']) }}"
                   class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-medium mf-touch
                   {{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300' }}">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-xl">shopping_cart</span>
                        Đơn mobile
                    </span>
                    @if(($pendingOrdersCount ?? 0) > 0)
                        <span class="min-w-[1.5rem] h-6 px-1.5 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                            {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                        </span>
                    @endif
                </a>
                <x-nav-link href="{{ route('admin.reports.index') }}" icon="analytics" :active="request()->routeIs('admin.reports.*')">{{ __('nav.reports') }}</x-nav-link>
                <x-nav-link href="{{ route('employees.index') }}" icon="badge" :active="request()->routeIs('employees.*')">Nhân viên</x-nav-link>
                <x-nav-link href="{{ route('store-settings.edit') }}" icon="storefront" :active="request()->routeIs('store-settings.*')">Thông tin cửa hàng</x-nav-link>
            @endif
            <x-nav-link href="{{ route('daily-reports.index') }}" icon="assignment" :active="request()->routeIs('daily-reports.*')">Báo cáo ngày</x-nav-link>
            @if(auth()->user()?->isAdmin() || auth()->user()?->isDriver())
                <x-nav-link href="{{ route('driver.index') }}" icon="local_shipping" :active="request()->routeIs('driver.*')">Tài xế</x-nav-link>
            @endif
            <a href="{{ url('/m') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-primary hover:bg-primary/10 mf-touch">
                <span class="material-symbols-outlined text-xl">smartphone</span>
                Portal khách (/m)
            </a>
        </nav>
    </div>
    <div class="mt-4 flex flex-col gap-2 border-t border-border-light pt-4 dark:border-border-dark">
        <x-nav-link href="{{ route('landing') }}" icon="public" :active="false">Trang chủ website</x-nav-link>
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-text-light transition-colors hover:bg-red-500/10 dark:text-text-dark mf-touch">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-sm font-medium">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>
