<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Customer Management Dashboard' }}</title>
    {{-- Theme Tailwind: tailwind.mobile.config.js --}}
    @vite(['resources/css/mobile.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-text-light dark:text-text-dark">
    <div class="flex h-screen w-full">
        {{ $sidebar ?? '' }}

        <div class="flex-1 flex flex-col overflow-hidden">
            {{ $topbar ?? '' }}

            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>

            <footer class="flex-shrink-0 bg-container-light dark:bg-container-dark border-t border-border-light dark:border-border-dark px-6 py-2 text-center text-xs text-gray-500 dark:text-gray-400">
                <p>© {{ date('Y') }} CMS Inc. All rights reserved. | System Version: 1.0.0</p>
            </footer>
        </div>
    </div>

    {{ $modal ?? '' }}
</body>
</html>
