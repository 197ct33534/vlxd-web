<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="theme-color" content="#007BFF"/>
    <title>@yield('title', $title ?? 'VLXD')</title>
    <link rel="manifest" href="/manifest.webmanifest"/>
    <link rel="stylesheet" href="/css/mobile-first.css"/>

    @vite(['resources/css/admin.css', 'resources/js/alpine.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>

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
    {{-- Theme Tailwind: tailwind.admin.config.js --}}
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark min-h-screen antialiased">
    <div class="flex h-screen min-h-[100dvh] w-full max-w-[100vw] overflow-x-hidden print:block print:h-auto print:min-h-0" x-data="{ toastVisible: false, toastMessage: '', isLoading: false }"
        @click="
            const link = $event.target.closest('a');
            if (link && link.href && !link.href.includes('#') && !link.getAttribute('href').startsWith('javascript:') && !link.hasAttribute('download') && link.target !== '_blank' && !link.hasAttribute('data-no-loading')) {
                isLoading = true;
            }
        ">

        <div
            x-show="$store.layout.sidebarOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$store.layout.closeSidebar()"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden print:hidden"
            x-cloak
        ></div>

        <x-sidebar />

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden print:block print:overflow-visible">
            <x-topbar>
                <x-slot:heading>@yield('page_heading', 'Quản trị')</x-slot:heading>
            </x-topbar>

            <main class="flex-1 min-w-0 w-full overflow-y-auto overflow-x-hidden px-3 pt-4 pb-[max(1rem,env(safe-area-inset-bottom))] sm:px-5 sm:pt-6 sm:pb-[max(1.5rem,env(safe-area-inset-bottom))] lg:px-8 lg:pt-8 lg:pb-[max(2rem,env(safe-area-inset-bottom))] mf-admin-main print:overflow-visible print:p-0">
                <div class="mx-auto w-full min-w-0 max-w-full">
                    @yield('content')
                </div>
            </main>

            <footer class="flex-shrink-0 print:hidden bg-container-light dark:bg-container-dark border-t border-border-light dark:border-border-dark px-3 sm:px-5 lg:px-8 py-2 text-center text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                <p>© {{ date('Y') }} VLXD. Portal khách: <a class="text-primary font-semibold" href="{{ url('/m') }}">/m</a></p>
            </footer>
        </div>

        <x-toast />
        <x-loading />
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));
        }
    </script>
</body>
</html>
