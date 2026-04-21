    <!DOCTYPE html>
<html class="light" lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>CMS Inc. - Import Invoice</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#2563eb",
                        "background-light": "#f8fafc",
                        "background-dark": "#0f172a",
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
<style type="text/tailwindcss">
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-item-active {
            background-color: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            border-right: 3px solid #2563eb;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex">
<aside class="w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col fixed h-full z-20">
<div class="p-6 flex items-center gap-3">
<div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons-outlined text-white text-xl">business_center</span>
</div>
<span class="font-bold text-xl tracking-tight text-slate-800 dark:text-white">CMS Inc.</span>
</div>
<nav class="flex-1 px-4 py-4 space-y-1">
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">dashboard</span>
<span class="font-medium text-sm">Dashboard</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">people_outline</span>
<span class="font-medium text-sm">Customers</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">badge</span>
<span class="font-medium text-sm">Employees</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">assessment</span>
<span class="font-medium text-sm">Daily Reports</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">folder_open</span>
<span class="font-medium text-sm">Projects</span>
</a>
<a class="sidebar-item-active flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-xl">description</span>
<span class="font-medium text-sm">Invoices</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">insights</span>
<span class="font-medium text-sm">Reports</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">settings</span>
<span class="font-medium text-sm">Settings</span>
</a>
</nav>
<div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-1">
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-icons-outlined text-xl">help_outline</span>
<span class="font-medium text-sm">Help</span>
</a>
<a class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition-colors" href="#">
<span class="material-icons-outlined text-xl">logout</span>
<span class="font-medium text-sm">Logout</span>
</a>
</div>
</aside>
<main class="flex-1 ml-64 flex flex-col h-screen overflow-hidden">
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 shrink-0">
<h1 class="text-lg font-semibold text-slate-800 dark:text-slate-200">Customer Management</h1>
<div class="flex items-center gap-6">
<div class="relative group">
<span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
<input class="pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-transparent focus:border-primary focus:ring-0 rounded-full text-sm w-64 transition-all" placeholder="Search..." type="text"/>
</div>
<div class="flex items-center gap-4">
<button class="relative text-slate-500 dark:text-slate-400 hover:text-primary transition-colors">
<span class="material-icons-outlined">notifications</span>
<span class="absolute -top-1 -right-1 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></span>
</button>
<div class="h-8 w-px bg-slate-200 dark:bg-slate-700"></div>
<div class="flex items-center gap-3">
<div class="text-right">
<p class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">Administrator</p>
<p class="text-[10px] text-slate-500 uppercase tracking-wider">Admin</p>
</div>
<div class="w-10 h-10 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center overflow-hidden border-2 border-slate-100 dark:border-slate-800">
<span class="material-icons-outlined text-slate-500">person</span>
</div>
</div>
</div>
</div>
</header>
<section class="flex-1 overflow-y-auto p-8 flex items-center justify-center bg-background-light dark:bg-background-dark">
<div class="w-full max-w-xl animate-in fade-in zoom-in duration-300">
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-100 dark:border-slate-800 overflow-hidden">
<div class="p-8">
<div class="flex flex-col items-center mb-8">
<div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mb-4">
<span class="material-icons-outlined text-primary text-4xl">folder</span>
</div>
<h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Import Hóa Đơn Bán Hàng</h2>
<p class="text-slate-500 dark:text-slate-400 mt-2 text-center text-sm">Tải lên tệp Excel của bạn để bắt đầu xử lý dữ liệu hóa đơn.</p>
</div>
<form class="space-y-6">
<div>
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2" for="customer-select">Chọn Khách hàng</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[20px]">person_search</span>
<select class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all appearance-none text-slate-600 dark:text-slate-300" id="customer-select">
<option disabled="" selected="" value="">Tìm kiếm hoặc chọn khách hàng</option>
<option value="1">Công ty TNHH Giải pháp Công nghệ ABC</option>
<option value="2">Tập đoàn Bán lẻ XYZ</option>
<option value="3">Hộ kinh doanh Nguyễn Văn A</option>
<option value="4">Cửa hàng Điện máy Sunrise</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
</div>
</div>
<div>
<label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Chọn file Excel (.xlsx, .xls)</label>
<div class="relative group">
<div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-8 transition-all group-hover:border-primary group-hover:bg-blue-50/30 dark:group-hover:bg-primary/5 flex flex-col items-center justify-center cursor-pointer">
<input accept=".xlsx, .xls" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" type="file"/>
<span class="material-icons-outlined text-4xl text-slate-400 group-hover:text-primary mb-3">cloud_upload</span>
<p class="text-sm font-medium text-slate-600 dark:text-slate-400">
<span class="text-primary font-bold">Nhấn để tải lên</span> hoặc kéo thả file vào đây
                                        </p>
<p class="text-xs text-slate-400 mt-1">Kích thước tối đa: 10MB</p>
</div>
</div>
</div>
<button class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition-all transform hover:scale-[1.02] active:scale-95 shadow-lg shadow-blue-200 dark:shadow-none" type="submit">
<span class="material-icons-outlined text-xl">upload_file</span>
                                Tiến hành Import
                            </button>
</form>
</div>
<div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-4 border-t border-slate-100 dark:border-slate-800">
<div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
<span class="material-icons-outlined text-sm">info</span>
<span>Hệ thống hỗ trợ tệp Excel chuẩn từ các phần mềm kế toán phổ biến.</span>
</div>
</div>
</div>
<p class="text-center mt-6 text-xs text-slate-400 dark:text-slate-500">
                    © 2026 CMS Inc. All rights reserved. | System Version: 1.0.0
                </p>
</div>
</section>
</main>
<div class="fixed bottom-6 right-6 flex items-center gap-2">
<button class="w-10 h-10 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center shadow-lg text-slate-600 dark:text-slate-300 hover:text-primary transition-colors" onclick="document.documentElement.classList.toggle('dark')">
<span class="material-icons-outlined">dark_mode</span>
</button>
</div>

</body></html>