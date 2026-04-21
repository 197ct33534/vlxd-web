<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Import Invoice - Data Preview | CMS Inc.</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#2563eb",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
                        "sidebar-light": "#ffffff",
                        "sidebar-dark": "#1e293b",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex">
<aside class="w-64 bg-sidebar-light dark:bg-sidebar-dark border-r border-slate-200 dark:border-slate-800 flex flex-col fixed h-full z-20 transition-colors duration-200">
<div class="p-6 flex items-center gap-3">
<div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
<span class="material-icons-round text-xl">receipt_long</span>
</div>
<h1 class="text-xl font-bold tracking-tight text-slate-800 dark:text-white">CMS Inc.</h1>
</div>
<nav class="flex-1 px-4 space-y-1">
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">dashboard</span>
<span class="font-medium">Dashboard</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-primary bg-blue-50 dark:bg-blue-900/20 rounded-lg transition-colors" href="#">
<span class="material-icons-round text-xl">group</span>
<span class="font-medium">Customers</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">badge</span>
<span class="font-medium">Employees</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">assessment</span>
<span class="font-medium">Daily Reports</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">folder</span>
<span class="font-medium">Projects</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">description</span>
<span class="font-medium">Invoices</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">analytics</span>
<span class="font-medium">Reports</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors group" href="#">
<span class="material-icons-round text-xl opacity-70 group-hover:opacity-100">settings</span>
<span class="font-medium">Settings</span>
</a>
</nav>
<div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-1">
<a class="flex items-center gap-3 px-3 py-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition-colors" href="#">
<span class="material-icons-round text-xl">help_outline</span>
<span class="text-sm font-medium">Help Center</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/10 rounded-lg transition-colors" href="#">
<span class="material-icons-round text-xl">logout</span>
<span class="text-sm font-medium">Logout</span>
</a>
</div>
</aside>
<main class="ml-64 flex-1 flex flex-col min-h-screen">
<header class="h-16 bg-sidebar-light dark:bg-sidebar-dark border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 sticky top-0 z-10 transition-colors duration-200">
<div class="flex items-center gap-4">
<span class="text-slate-400">/</span>
<h2 class="text-lg font-semibold text-slate-800 dark:text-white">Customer Management</h2>
</div>
<div class="flex items-center gap-6">
<div class="relative group">
<span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-focus-within:text-primary transition-colors">search</span>
<input class="bg-slate-100 dark:bg-slate-800 border-none rounded-full pl-10 pr-4 py-1.5 text-sm w-64 focus:ring-2 focus:ring-primary/20 dark:text-white placeholder:text-slate-400 transition-all" placeholder="Search..." type="text"/>
</div>
<button class="relative p-1 text-slate-400 hover:text-primary transition-colors">
<span class="material-icons-round text-2xl">notifications</span>
<span class="absolute top-0 right-0 w-2 h-2 bg-red-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
</button>
<div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-800">
<div class="text-right">
<p class="text-sm font-semibold text-slate-800 dark:text-white leading-tight">Administrator</p>
<p class="text-xs text-slate-500">Admin Level</p>
</div>
<div class="w-9 h-9 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center text-slate-500">
<span class="material-icons-round">person</span>
</div>
</div>
</div>
</header>
<div class="p-8 max-w-6xl mx-auto w-full">
<div class="mb-8 text-center">
<div class="inline-flex items-center justify-center p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
<span class="material-icons-round text-primary text-3xl">task_alt</span>
</div>
<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kết quả đọc file hóa đơn</h1>
<p class="text-slate-500 dark:text-slate-400 mt-2">Vui lòng kiểm tra lại thông tin trước khi xác nhận nhập vào hệ thống.</p>
</div>
<div class="bg-white dark:bg-sidebar-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-200">
<div class="p-8 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
<div>
<div class="flex items-center gap-2 mb-4">
<span class="material-icons-round text-primary">person_outline</span>
<h3 class="font-bold text-lg text-slate-800 dark:text-white uppercase tracking-wide">Thông tin khách hàng</h3>
</div>
<div class="space-y-3 ml-1">
<div class="flex items-center gap-4">
<span class="text-slate-500 w-24 text-sm">Tên khách:</span>
<span class="font-semibold text-slate-900 dark:text-slate-100">Tiến</span>
</div>
<div class="flex items-start gap-4">
<span class="text-slate-500 w-24 text-sm">Địa chỉ:</span>
<span class="text-slate-700 dark:text-slate-300">đường 25</span>
</div>
<div class="flex items-center gap-4">
<span class="text-slate-500 w-24 text-sm">Điện thoại:</span>
<span class="text-slate-700 dark:text-slate-300">—</span>
</div>
</div>
</div>
<div class="md:text-right flex flex-col justify-end">
<div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700 inline-block ml-auto shadow-sm">
<div class="flex flex-col md:items-end gap-1">
<span class="text-xs font-medium text-slate-500 uppercase">Ngày hóa đơn cuối</span>
<span class="text-slate-900 dark:text-white font-semibold">23/1/2025</span>
</div>
<div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-col md:items-end gap-1">
<span class="text-xs font-medium text-slate-500 uppercase">Tổng tiền phải thanh toán</span>
<span class="text-2xl font-black text-primary">17.320.000 đ</span>
<p class="text-[10px] text-slate-400 italic">Mười bảy triệu ba trăm hai mươi nghìn đồng</p>
</div>
</div>
</div>
</div>
</div>
<div class="overflow-x-auto custom-scrollbar">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-widest border-b border-slate-100 dark:border-slate-800">
<th class="py-4 px-6 text-center w-16">STT</th>
<th class="py-4 px-6">Ngày</th>
<th class="py-4 px-6 min-w-[200px]">Tên vật liệu</th>
<th class="py-4 px-6">ĐVT</th>
<th class="py-4 px-6 text-right">Số lượng</th>
<th class="py-4 px-6 text-right">Đơn giá</th>
<th class="py-4 px-6 text-right">Thành tiền</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">1</td>
<td class="py-4 px-6 text-slate-500 text-sm">—</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Cát To</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Xe</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">1</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">550.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">550.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">2</td>
<td class="py-4 px-6 text-slate-500 text-sm">—</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Đá 1x2</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Xe</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">1</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">850.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">850.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">3</td>
<td class="py-4 px-6 text-slate-500 text-sm">—</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Xi Măng Sao Mai</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Bao</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">4</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">95.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">380.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">4</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400 text-sm">20/1/2025</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Gạch Ống</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Viên</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">2000</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">1.400</td>
<td class="py-4 px-6 text-right font-semibold text-primary">2.800.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">5</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400 text-sm">20/1/2025</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Gạch Đinh</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Viên</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">100</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">1.400</td>
<td class="py-4 px-6 text-right font-semibold text-primary">140.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">6</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400 text-sm">20/1/2025</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Đỏ Mi</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">—</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">300</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">1.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">300.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">7</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400 text-sm">20/1/2025</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Cát To</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Xe</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">2</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">550.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">1.100.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">8</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400 text-sm">20/1/2025</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Xi Măng Sao Mai</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Bao</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">10</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">95.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">950.000</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="py-4 px-6 text-center text-slate-500 text-sm">9</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400 text-sm">23/1/2025</td>
<td class="py-4 px-6 font-medium text-slate-800 dark:text-slate-200">Xi Măng Sao Mai</td>
<td class="py-4 px-6 text-slate-600 dark:text-slate-400">Bao</td>
<td class="py-4 px-6 text-right text-slate-800 dark:text-slate-200">40</td>
<td class="py-4 px-6 text-right text-slate-600 dark:text-slate-400">95.000</td>
<td class="py-4 px-6 text-right font-semibold text-primary">3.800.000</td>
</tr>
</tbody>
</table>
</div>
<div class="p-8 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-slate-200 dark:border-slate-800">
<div class="flex items-center gap-3 text-sm text-slate-500">
<span class="material-icons-round text-amber-500">info</span>
<span>Dữ liệu đã được định dạng và kiểm tra trùng lặp.</span>
</div>
<div class="flex gap-4">
<button class="px-6 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-all">
                            Hủy bỏ
                        </button>
<button class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all flex items-center gap-2">
<span class="material-icons-round text-lg">cloud_upload</span>
                            Tiến hành import vào database
                        </button>
</div>
</div>
</div>
<footer class="mt-12 text-center text-slate-400 text-xs">
<p>© 2025 CMS Inc. All rights reserved. | System Version: 1.2.0 | Process ID: #IMP-9421</p>
</footer>
</div>
</main>
<div class="fixed bottom-6 right-6 z-50">
<button class="w-12 h-12 bg-white dark:bg-sidebar-dark shadow-xl rounded-full border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-yellow-400 transition-transform active:scale-95" onclick="document.documentElement.classList.toggle('dark')">
<span class="material-icons-round">dark_mode</span>
</button>
</div>

</body></html>