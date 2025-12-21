<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'eCommerce Dashboard')</title>
    {{-- thêm link css / vite / mix ở đây nếu cần --}}
    {{-- Nếu đã build với Vite (manifest tồn tại) thì dùng @vite, nếu không dùng fallback tĩnh --}}

    <!-- Fallback tĩnh khi chưa build frontend (copy build vào public/frontend hoặc public/build) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Fallback tĩnh (nếu bạn copy frontend build -> public/frontend): --}}
    {{-- <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}"> --}}
</head>

<body x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{ 'dark bg-gray-900': darkMode === true }">

    @include('partials.preloader')

    <div class="flex h-screen overflow-hidden">
        @include('partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            @include('partials.overlay')
            @include('partials.header')

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Nếu Alpine chưa được bundle vào resources/js/app.js, dùng CDN: --}}

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/index.js') }}" defer></script>




</html>
</body>
{{-- thêm script / vite / alpine ở đây --}}
</body>

</html>
