<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý hóa đơn')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .nav-link {
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            transform: translateY(-2px);
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    {{-- Thanh tiêu đề --}}
    <header class="bg-blue-700 text-white shadow-lg sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">📊</span>
                <h1 class="text-xl font-bold tracking-tight">Quản lý Hóa Đơn VLXD</h1>
            </div>
            <nav class="flex space-x-6">
                <a href="{{ url('/import-invoice') }}" class="nav-link px-3 py-2 rounded-md text-sm font-medium">Import
                    Hóa Đơn</a>
                <a href="{{ url('/') }}" class="nav-link px-3 py-2 rounded-md text-sm font-medium">Trang chủ</a>
            </nav>
        </div>
    </header>

    {{-- Nội dung --}}
    <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-gray-300 py-6 text-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-sm">&copy; {{ date('Y') }} - Hệ thống quản lý VLXD. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>
