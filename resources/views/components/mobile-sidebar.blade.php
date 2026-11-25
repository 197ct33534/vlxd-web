{{--
    Sidebar admin cho các trang mobile độc lập (không dùng layouts.dashboard).
    Đặt ngay sau <body>; nút mở: @click="$store.layout.toggleSidebar()".
    Store khai báo giống layouts.dashboard — script thường chạy trước Alpine
    (Alpine được load dạng module nên luôn chạy sau khi HTML parse xong).
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('layout', {
            sidebarOpen: false,
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            },
            closeSidebar() {
                this.sidebarOpen = false;
            },
        });
    });
</script>

<div
    x-show="$store.layout.sidebarOpen"
    x-transition:enter="transition-opacity ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="$store.layout.closeSidebar()"
    class="fixed inset-0 z-[60] bg-black/50 lg:hidden"
    x-cloak
></div>

{{-- z-[70]: nằm trên header/nav dính (một số trang mobile dùng z-50). --}}
<div class="relative z-[70]">
    <x-sidebar />
</div>
