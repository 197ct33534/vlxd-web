<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
    <meta name="theme-color" content="#007BFF"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="default"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="manifest" href="/manifest.webmanifest"/>
    <link rel="icon" href="/images/logo/logo-icon.svg"/>
    <title>@yield('title', 'VLXD Đặt hàng')</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <link rel="stylesheet" href="/css/mobile-first.css"/>
    {{-- Theme Tailwind: tailwind.portal.config.js --}}
    @vite(['resources/css/portal.css', 'resources/js/alpine.js'])
    <style>
        body { font-family: 'Be Vietnam Pro', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
@php($store = \App\Models\StoreInfo::current())
@php($hasPortalNav = (bool) session('portal_customer_id'))
<body class="bg-surface text-ink min-h-screen {{ $hasPortalNav ? 'has-portal-nav pb-40' : 'pb-28' }}">

    <header class="sticky top-0 z-30 bg-white/95 border-b border-gray-200 backdrop-blur px-4 py-3">
        <div class="flex items-center justify-between gap-3 max-w-lg mx-auto">
            <div class="min-w-0">
                <p class="text-xs text-gray-500 truncate">{{ $store->name ?? 'VLXD' }}</p>
                <h1 class="text-lg font-bold truncate">@yield('heading', 'Đặt hàng')</h1>
            </div>
            <div class="flex items-center gap-1 shrink-0">
                @if($store->telHref())
                    <a href="{{ $store->telHref() }}" class="mf-touch p-2 text-primary" aria-label="Gọi shop">
                        <span class="material-symbols-outlined">call</span>
                    </a>
                @endif
                @if($store->zaloHref())
                    <a href="{{ $store->zaloHref() }}" target="_blank" rel="noopener" class="mf-touch p-2 text-primary" aria-label="Zalo">
                        <span class="material-symbols-outlined">chat</span>
                    </a>
                @endif
                @hasSection('header_actions')
                    @yield('header_actions')
                @endif
            </div>
        </div>
    </header>

    <main class="max-w-lg mx-auto px-4 pt-4">
        @if(session('success'))
            <div class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    {{-- Sticky CTA chỉ khi chưa login (đã có icon Gọi/Zalo trên header khi đã vào app) --}}
    @unless($hasPortalNav)
        <x-store-cta :store="$store" />
    @endunless

    @hasSection('bottom_nav')
        @yield('bottom_nav')
    @else
        @include('portal.partials.bottom-nav')
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
