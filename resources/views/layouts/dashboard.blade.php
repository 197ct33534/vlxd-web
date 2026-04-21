<!DOCTYPE html>
<html class="light" lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', $title ?? 'CMS Inc.')</title>
    
    {{-- Tailwind & Plugins --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    {{-- Fonts --}}
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
    {{-- Alpine.js (sau script đăng ký store) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tailwind Config --}}
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#007BFF",
                        "background-light": "#F5F6F8",
                        "background-dark": "#101922",
                        "text-light": "#212529",
                        "text-dark": "#f5f7f8",
                        "container-light": "#ffffff",
                        "container-dark": "#1a242d",
                        "border-light": "#e9ecef",
                        "border-dark": "#2c3a47",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                    boxShadow: {
                        'subtle': '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
                    }
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark min-h-screen antialiased">
    <!-- Main App Container with Global State (Toast & Loading) -->
    <div class="flex h-screen min-h-[100dvh] w-full max-w-[100vw] overflow-x-hidden" x-data="{ toastVisible: false, toastMessage: '', isLoading: false }" 
        @click="
            const link = $event.target.closest('a');
            if (link && link.href && !link.href.includes('#') && !link.getAttribute('href').startsWith('javascript:') && !link.hasAttribute('download') && link.target !== '_blank') {
                isLoading = true;
            }
        ">
        
        <!-- Mobile overlay -->
        <div
            x-show="$store.layout.sidebarOpen"
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="$store.layout.closeSidebar()"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            x-cloak
        ></div>

        <!-- Sidebar (drawer trên mobile, cố định trên desktop) -->
        <x-sidebar />

        <!-- Main Content Area -->
        <div class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <!-- Topbar -->
            <x-topbar>
                <x-slot:heading>@yield('page_heading', 'Quản trị')</x-slot:heading>
            </x-topbar>

            <!-- Page Content -->
            <main class="flex-1 min-w-0 w-full overflow-y-auto overflow-x-hidden px-3 pt-4 pb-[max(1rem,env(safe-area-inset-bottom))] sm:px-5 sm:pt-6 sm:pb-[max(1.5rem,env(safe-area-inset-bottom))] lg:px-8 lg:pt-8 lg:pb-[max(2rem,env(safe-area-inset-bottom))]">
                <div class="mx-auto w-full min-w-0 max-w-full">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="flex-shrink-0 bg-container-light dark:bg-container-dark border-t border-border-light dark:border-border-dark px-3 sm:px-5 lg:px-8 py-2 text-center text-[10px] sm:text-xs text-gray-500 dark:text-gray-400">
                <p>© {{ date('Y') }} CMS Inc. All rights reserved. | System Version: 1.0.0</p>
            </footer>
        </div>

        <!-- Global Toast Notification -->
        <x-toast />
        
        <!-- Global Loading Indicator -->
        <x-loading />
    </div>
</body>
</html>
