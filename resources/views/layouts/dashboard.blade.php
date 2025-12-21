<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'CMS Inc.' }}</title>
    
    {{-- Tailwind & Plugins --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    
    {{-- Alpine.js --}}
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
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
    <!-- Main App Container with Global State (Toast) -->
    <div class="flex h-screen w-full" x-data="{ toastVisible: false, toastMessage: '' }">
        
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Topbar -->
            <x-topbar />

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="flex-shrink-0 bg-container-light dark:bg-container-dark border-t border-border-light dark:border-border-dark px-6 py-2 text-center text-xs text-gray-500 dark:text-gray-400">
                <p>© {{ date('Y') }} CMS Inc. All rights reserved. | System Version: 1.0.0</p>
            </footer>
        </div>

        <!-- Global Toast Notification -->
        <x-toast />
    </div>
</body>
</html>
