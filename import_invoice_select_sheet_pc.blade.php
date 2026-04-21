<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>CMS Inc. - Import Invoice</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
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
        .custom-radio:checked + label {
            border-color: #2563eb;
            background-color: rgba(37, 99, 235, 0.05);
        }
        .dark .custom-radio:checked + label {
            border-color: #2563eb;
            background-color: rgba(37, 99, 235, 0.1);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen flex">
<aside class="w-64 bg-sidebar-light dark:bg-sidebar-dark border-r border-slate-200 dark:border-slate-800 flex flex-col fixed inset-y-0 z-50 transition-colors duration-200">
<div class="p-6 flex items-center gap-3">
<div class="bg-primary p-2 rounded-lg">
<span class="material-icons-outlined text-white">business_center</span>
</div>
<span class="text-xl font-bold tracking-tight text-slate-800 dark:text-white">CMS Inc.</span>
</div>
<nav class="flex-1 px-4 space-y-1 mt-4">
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">dashboard</span>
<span class="text-sm font-medium">Dashboard</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">people_alt</span>
<span class="text-sm font-medium">Customers</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">badge</span>
<span class="text-sm font-medium">Employees</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">summarize</span>
<span class="text-sm font-medium">Daily Reports</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">folder</span>
<span class="text-sm font-medium">Projects</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 bg-primary/10 text-primary rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">receipt_long</span>
<span class="text-sm font-semibold">Invoices</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">assessment</span>
<span class="text-sm font-medium">Reports</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">settings</span>
<span class="text-sm font-medium">Settings</span>
</a>
</nav>
<div class="p-4 border-t border-slate-200 dark:border-slate-800 space-y-1">
<a class="flex items-center gap-3 px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">help_outline</span>
<span class="text-sm font-medium">Help</span>
</a>
<a class="flex items-center gap-3 px-3 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" href="#">
<span class="material-icons-outlined text-[20px]">logout</span>
<span class="text-sm font-medium">Logout</span>
</a>
</div>
</aside>
<main class="flex-1 ml-64 flex flex-col">
<header class="h-16 bg-white dark:bg-sidebar-dark border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-8 sticky top-0 z-40 transition-colors duration-200">
<h1 class="text-lg font-semibold text-slate-800 dark:text-white">Customer Management</h1>
<div class="flex items-center gap-6">
<div class="relative w-64">
<span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
<input class="w-full pl-10 pr-4 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm focus:ring-primary focus:border-primary dark:text-white" placeholder="Search..." type="text"/>
</div>
<button class="relative text-slate-500 dark:text-slate-400 hover:text-primary transition-colors">
<span class="material-icons-outlined">notifications</span>
<span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full border-2 border-white dark:border-sidebar-dark"></span>
</button>
<div class="flex items-center gap-3 border-l border-slate-200 dark:border-slate-700 pl-6">
<div class="text-right">
<p class="text-sm font-semibold text-slate-800 dark:text-white leading-none">Administrator</p>
<p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wider">Admin</p>
</div>
<div class="h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-500 overflow-hidden">
<span class="material-icons-outlined">person</span>
</div>
</div>
</div>
</header>
<section class="flex-1 p-8 flex items-center justify-center relative overflow-hidden">
<div class="absolute top-0 left-0 w-full h-full pointer-events-none">
<div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
<div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
</div>
<div class="max-w-md w-full bg-white dark:bg-sidebar-dark rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-8 relative z-10">
<div class="text-center mb-8">
<div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 rounded-2xl mb-4">
<span class="material-icons-outlined text-3xl">folder</span>
</div>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Select Sheet to Import</h2>
<p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        The uploaded file contains multiple sheets. Please select the sheet you want to import data from:
                    </p>
</div>
<form class="space-y-3">
<div class="relative">
<input checked="" class="hidden peer custom-radio" id="sheet1" name="sheet" type="radio"/>
<label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 transition-all" for="sheet1">
<div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center peer-checked:border-primary">
<div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform duration-200" id="dot1"></div>
</div>
<span class="text-sm font-medium text-slate-700 dark:text-slate-200">Sheet1</span>
</label>
</div>
<div class="relative">
<input class="hidden peer custom-radio" id="sheet2" name="sheet" type="radio"/>
<label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 transition-all" for="sheet2">
<div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center peer-checked:border-primary">
<div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform duration-200" id="dot2"></div>
</div>
<span class="text-sm font-medium text-slate-700 dark:text-slate-200">1-2_2026</span>
</label>
</div>
<div class="relative">
<input class="hidden peer custom-radio" id="sheet3" name="sheet" type="radio"/>
<label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 cursor-pointer hover:border-primary/50 dark:hover:border-primary/50 transition-all" for="sheet3">
<div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-600 flex items-center justify-center peer-checked:border-primary">
<div class="w-2.5 h-2.5 rounded-full bg-primary scale-0 transition-transform duration-200" id="dot3"></div>
</div>
<span class="text-sm font-medium text-slate-700 dark:text-slate-200">11_2_2026</span>
</label>
</div>
<button class="w-full mt-8 bg-primary hover:bg-blue-700 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-primary/30 transition-all flex items-center justify-center gap-2 group" type="submit">
<span>Continue Import</span>
<span class="material-icons-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
</button>
</form>
</div>
</section>
<footer class="py-4 text-center border-t border-slate-200 dark:border-slate-800 transition-colors duration-200">
<p class="text-[11px] text-slate-400 dark:text-slate-500">
                © 2026 CMS Inc. All rights reserved. | System Version: 1.0.0
            </p>
</footer>
</main>
<script>
        // Custom Radio Selection Logic for visuals
        const radios = document.querySelectorAll('input[name="sheet"]');
        const updateDots = () => {
            radios.forEach(radio => {
                const dot = radio.nextElementSibling.querySelector('div div');
                if(radio.checked) {
                    dot.classList.remove('scale-0');
                    dot.classList.add('scale-100');
                    radio.nextElementSibling.classList.add('border-primary');
                } else {
                    dot.classList.add('scale-0');
                    dot.classList.remove('scale-100');
                    radio.nextElementSibling.classList.remove('border-primary');
                }
            });
        };
        radios.forEach(r => r.addEventListener('change', updateDots));
        updateDots(); // Initial call
        // Theme Toggle Support (Optional helper)
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>

</body></html>