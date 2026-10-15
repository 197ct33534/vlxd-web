@php
    $customerId = session('portal_customer_id');
@endphp

@if($customerId)
<nav class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 pb-[env(safe-area-inset-bottom)]">
    <div class="max-w-lg mx-auto grid grid-cols-5 text-[10px] sm:text-xs font-semibold text-gray-500">
        <a href="{{ route('portal.home') }}" class="flex flex-col items-center gap-0.5 py-2 mf-touch {{ request()->routeIs('portal.home') ? 'text-primary' : '' }}">
            <span class="material-symbols-outlined text-xl">home</span>
            Trang chủ
        </a>
        <a href="{{ route('portal.orders.create') }}" class="flex flex-col items-center gap-0.5 py-2 mf-touch {{ request()->routeIs('portal.orders.create') ? 'text-primary' : '' }}">
            <span class="material-symbols-outlined text-xl">add_shopping_cart</span>
            Đặt hàng
        </a>
        <a href="{{ route('portal.calculator') }}" class="flex flex-col items-center gap-0.5 py-2 mf-touch {{ request()->routeIs('portal.calculator') ? 'text-primary' : '' }}">
            <span class="material-symbols-outlined text-xl">calculate</span>
            Tính KL
        </a>
        <a href="{{ route('portal.orders.index') }}" class="flex flex-col items-center gap-0.5 py-2 mf-touch {{ request()->routeIs('portal.orders.index') || request()->routeIs('portal.orders.show') ? 'text-primary' : '' }}">
            <span class="material-symbols-outlined text-xl">receipt_long</span>
            Đơn hàng
        </a>
        <a href="{{ route('portal.logout') }}" onclick="event.preventDefault(); document.getElementById('portal-logout').submit();" class="flex flex-col items-center gap-0.5 py-2 mf-touch">
            <span class="material-symbols-outlined text-xl">logout</span>
            Thoát
        </a>
    </div>
    <form id="portal-logout" action="{{ route('portal.logout') }}" method="POST" class="hidden">@csrf</form>
</nav>
@endif
