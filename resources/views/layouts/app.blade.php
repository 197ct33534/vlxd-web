<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    
    <!-- Scripts & Styles -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#135bec",
                        "background-light": "#f6f6f8",
                        "background-dark": "#101622",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .active-icon {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100">
    <div class="flex h-screen overflow-hidden">
        <!-- SideNavBar -->
        <aside class="w-64 border-r border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col">
            <div class="p-6 flex items-center gap-3">
                <div class="bg-primary rounded-lg p-2 text-white">
                    <span class="material-symbols-outlined block">grid_view</span>
                </div>
                <div>
                    <h1 class="text-base font-bold leading-none">Admin Panel</h1>
                    <p class="text-xs text-slate-500 mt-1">Laravel Dashboard</p>
                </div>
            </div>
            <nav class="flex-1 px-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} cursor-pointer">
                    <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'active-icon' : '' }}">dashboard</span>
                    <p class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'font-bold' : '' }}">Dashboard</p>
                </a>
                <a href="{{ route('images.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('images.index') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }} cursor-pointer">
                    <span class="material-symbols-outlined {{ request()->routeIs('images.index') ? 'active-icon' : '' }}">image</span>
                    <p class="text-sm font-medium {{ request()->routeIs('images.index') ? 'font-bold' : '' }}">Image Manager</p>
                </a>
                <!-- Other links placeholders -->
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                    <span class="material-symbols-outlined">folder</span>
                    <p class="text-sm font-medium">Collections</p>
                </div>
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
                    <span class="material-symbols-outlined">settings</span>
                    <p class="text-sm font-medium">Settings</p>
                </div>
            </nav>
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3 p-2">
                    <div class="size-8 rounded-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDjwnId2Fs46vjGIvSMRc5YWql-BGLs2s8fe4qhiDc9gRKzVP5vpN6l42YjSzRCw_HPIPYh022CF15wXj5jTxfpgnySr8jUUYk4BtK5VE2tkjExHCOVU0JgsfxKhmhFzOk_jFLvFJiEhLbui4P0IhynepLDboAQHLtPKv68cKsQWMm2PA6v1MWMNihic09z3MrFNKFs7gQXbO07LcMj_0SMClo4NQj3bOBURxMZf2E9i7fYJw41U8hc9G1Ba5veb5M-cA6pMkaIHyI')"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="material-symbols-outlined text-slate-400 text-sm hover:text-red-500 transition-colors">logout</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col overflow-hidden">
            @yield('content')
        </main>
    </div>
</body>
</html>
