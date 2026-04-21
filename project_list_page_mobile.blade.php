        <!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Projects Dashboard - Mobile View</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style type="text/tailwindcss">
        :root {
            --primary: #2a8ff4;
            --background-light: #f5f7f8;
            --background-dark: #101922;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#2a8ff4",
                        "background-light": "#f5f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
<div class="relative flex min-h-screen w-full flex-col">
<header class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark px-4 py-3 sticky top-0 z-30 shadow-sm">
<div class="flex items-center gap-3">
<button class="p-2 -ml-2 text-gray-600 dark:text-gray-300">
<span class="material-symbols-outlined">menu</span>
</button>
<h1 class="text-gray-900 dark:text-white text-lg font-bold">Projects</h1>
</div>
<div class="flex items-center gap-2">
<button class="p-2 text-gray-600 dark:text-gray-300">
<span class="material-symbols-outlined text-2xl">search</span>
</button>
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-8 border border-gray-200" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuBggkpEsjKJQFiGqNty6t3PlyyNA_zGCP5ig7LUrzsJkWtjXmx2_LTSNXMoS63PHMAtd7qXq7Ghk4FCL9_pPzNCEYZ-rrT5fRfJWWMQzv06IbgoCFqyRiNhQHW3V7Sziul_viSOR20SM-hG1tLinFvJBgb6EpoVXU5jmlhN9H951CQVr2_PBRTTXOL_ilE32IRSW90JN2Ow9ziZ1qTMuX1-G8yYsB9xwjl_gUzwfPtxIAzbkOYpUU7GL-__vWQhW7msk5KyrCf703M");'></div>
</div>
</header>
<main class="flex-1 p-4 pb-24">
<div class="flex items-center justify-between mb-6">
<div>
<h2 class="text-2xl font-bold text-gray-900 dark:text-white">Active Projects</h2>
<p class="text-sm text-gray-500 dark:text-gray-400">Showing 5 projects</p>
</div>
<button class="flex items-center gap-1 h-10 px-4 bg-primary text-white rounded-lg text-sm font-semibold shadow-md hover:bg-primary/90 transition-colors">
<span class="material-symbols-outlined text-lg">add</span>
<span>New</span>
</button>
</div>
<div class="flex gap-2 mb-6 overflow-x-auto pb-1 no-scrollbar">
<button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-medium shadow-sm whitespace-nowrap">
<span class="material-symbols-outlined text-lg">filter_list</span>
<span>Filters</span>
</button>
<button class="px-4 py-2 bg-primary/10 text-primary border border-primary/20 rounded-full text-sm font-medium whitespace-nowrap">
                    All Status
                </button>
<button class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-medium whitespace-nowrap">
                    Completed
                </button>
</div>
<div class="space-y-4">
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
<div class="p-4">
<div class="flex justify-between items-start mb-3">
<div>
<h3 class="font-bold text-gray-900 dark:text-white text-lg">Project Alpha</h3>
<p class="text-sm text-gray-500 dark:text-gray-400">Customer A</p>
</div>
<span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Completed</span>
</div>
<div class="grid grid-cols-2 gap-4 py-3 border-y border-gray-100 dark:border-gray-700 mb-3">
<div>
<p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Start Date</p>
<p class="text-sm font-medium">Jan 15, 2023</p>
</div>
<div>
<p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Invoices</p>
<p class="text-sm font-medium">12 Invoices</p>
</div>
</div>
<div class="flex justify-between items-center">
<a class="text-primary text-sm font-semibold flex items-center gap-1" href="#">
                                View Details <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
<div class="flex gap-2">
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
<span class="material-symbols-outlined text-lg">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 border border-red-100 dark:border-red-900/30">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</div>
</div>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
<div class="p-4">
<div class="flex justify-between items-start mb-3">
<div>
<h3 class="font-bold text-gray-900 dark:text-white text-lg">Project Beta</h3>
<p class="text-sm text-gray-500 dark:text-gray-400">Customer B</p>
</div>
<span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">In Progress</span>
</div>
<div class="grid grid-cols-2 gap-4 py-3 border-y border-gray-100 dark:border-gray-700 mb-3">
<div>
<p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Start Date</p>
<p class="text-sm font-medium">Feb 20, 2023</p>
</div>
<div>
<p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Invoices</p>
<p class="text-sm font-medium">8 Invoices</p>
</div>
</div>
<div class="flex justify-between items-center">
<a class="text-primary text-sm font-semibold flex items-center gap-1" href="#">
                                View Details <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
<div class="flex gap-2">
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
<span class="material-symbols-outlined text-lg">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 border border-red-100 dark:border-red-900/30">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</div>
</div>
</div>
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
<div class="p-4">
<div class="flex justify-between items-start mb-3">
<div>
<h3 class="font-bold text-gray-900 dark:text-white text-lg">Project Gamma</h3>
<p class="text-sm text-gray-500 dark:text-gray-400">Customer A</p>
</div>
<span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">On Hold</span>
</div>
<div class="grid grid-cols-2 gap-4 py-3 border-y border-gray-100 dark:border-gray-700 mb-3">
<div>
<p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Start Date</p>
<p class="text-sm font-medium">Mar 10, 2023</p>
</div>
<div>
<p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Invoices</p>
<p class="text-sm font-medium">5 Invoices</p>
</div>
</div>
<div class="flex justify-between items-center">
<a class="text-primary text-sm font-semibold flex items-center gap-1" href="#">
                                View Details <span class="material-symbols-outlined text-sm">arrow_forward</span>
</a>
<div class="flex gap-2">
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
<span class="material-symbols-outlined text-lg">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 text-red-500 border border-red-100 dark:border-red-900/30">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</div>
</div>
</div>
</div>
</main>
<button class="fixed bottom-6 right-6 w-14 h-14 bg-primary text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 active:scale-95 transition-transform z-40">
<span class="material-symbols-outlined text-3xl">add</span>
</button>
<nav class="fixed bottom-0 left-0 right-0 bg-white dark:bg-background-dark border-t border-gray-200 dark:border-gray-700 flex justify-around items-center py-2 px-4 z-50">
<a class="flex flex-col items-center gap-1 text-gray-400" href="#">
<span class="material-symbols-outlined">dashboard</span>
<span class="text-[10px]">Home</span>
</a>
<a class="flex flex-col items-center gap-1 text-gray-400" href="#">
<span class="material-symbols-outlined">group</span>
<span class="text-[10px]">Customers</span>
</a>
<a class="flex flex-col items-center gap-1 text-primary font-bold" href="#">
<span class="material-symbols-outlined">folder</span>
<span class="text-[10px]">Projects</span>
</a>
<a class="flex flex-col items-center gap-1 text-gray-400" href="#">
<span class="material-symbols-outlined">receipt_long</span>
<span class="text-[10px]">Invoices</span>
</a>
<a class="flex flex-col items-center gap-1 text-gray-400" href="#">
<span class="material-symbols-outlined">settings</span>
<span class="text-[10px]">Settings</span>
</a>
</nav>
<footer class="px-6 py-4 pb-20 border-t border-gray-200 dark:border-gray-700 text-center text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-background-dark">
<p>ACME Corp © 2023</p>
<p class="mt-1">System Version 2.1.0</p>
</footer>
</div>

</body></html>