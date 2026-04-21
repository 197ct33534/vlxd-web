<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Edit Invoice - Mobile View</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-[#333333] dark:text-gray-200">
<div class="relative flex h-auto min-h-screen w-full flex-col overflow-x-hidden">
<header class="flex items-center justify-between px-4 py-3 bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
<div class="flex items-center gap-3">
<button class="flex items-center justify-center p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
<span class="material-symbols-outlined">menu</span>
</button>
<h2 class="text-base font-bold text-[#111418] dark:text-white">Invoice Management</h2>
</div>
<div class="flex items-center gap-2">
<button class="flex items-center justify-center h-10 w-10 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
<span class="material-symbols-outlined">notifications</span>
</button>
<div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-8" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDVz9Mq-71_yvM1pSujyrxybGeI8DIrVqSe8ZXbaTyT1pSdlG48gW-m8nxRyIZjEbNP4qNGW8Ovg5cCaxWawm7SL4fmwD206YFBgwspHUcW4kShkXKbEDBnpfgdmJbD7ZAEU2alw6OJ7COwywYjMqZkuUFEXqzgt_npol92zj9sZ-b9jM1DNZnJJhr1WF12rK8M4JVcbus8dlhCypfP8pHHCGV9bSW-EsdFeyoKAqSwDM8vKRjMlrFuynpx0IpPSfubVzZVFwqpXkE");'></div>
</div>
</header>
<main class="flex-1 p-4 bg-background-light dark:bg-background-dark">
<div class="max-w-md mx-auto">
<div class="mb-6">
<h1 class="text-[#111418] dark:text-white text-2xl font-bold tracking-tight">Edit Invoice</h1>
<p class="text-gray-500 dark:text-gray-400 text-sm font-medium">#INV-2023-0123</p>
</div>
<div class="space-y-6">
<section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
<h3 class="text-base font-bold mb-4 text-[#111418] dark:text-white flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">person</span>
                            Bill To
                        </h3>
<div class="space-y-4">
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Client Name</span>
<input class="form-input w-full rounded-lg text-[#111418] dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary" value="Acme Corporation"/>
</label>
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Client Email</span>
<input class="form-input w-full rounded-lg text-[#111418] dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary" type="email" value="contact@acme.com"/>
</label>
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Client Address</span>
<input class="form-input w-full rounded-lg text-[#111418] dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary" value="123 Innovation Drive, Tech City"/>
</label>
</div>
</section>
<section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
<h3 class="text-base font-bold mb-4 text-[#111418] dark:text-white flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">description</span>
                            Invoice Details
                        </h3>
<div class="space-y-4">
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Invoice Number</span>
<input class="form-input w-full rounded-lg text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 h-11 text-sm" readonly="" value="#INV-2023-0123"/>
</label>
<div class="grid grid-cols-1 gap-4">
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Issue Date</span>
<input class="form-input w-full rounded-lg text-[#111418] dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary" type="date" value="2023-10-26"/>
</label>
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Due Date</span>
<input class="form-input w-full rounded-lg text-[#111418] dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-11 text-sm focus:ring-primary focus:border-primary" type="date" value="2023-11-25"/>
</label>
</div>
</div>
</section>
<section class="space-y-4">
<div class="flex items-center justify-between">
<h3 class="text-base font-bold text-[#111418] dark:text-white flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">list_alt</span>
                                Line Items
                            </h3>
</div>
<div class="bg-white dark:bg-gray-900/50 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
<div class="flex justify-between items-start mb-3">
<div class="flex-1">
<input class="w-full bg-transparent border-none p-0 text-sm font-semibold text-[#111418] dark:text-white focus:ring-0" value="Web Design Services"/>
</div>
<button class="text-red-500 p-1 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
<div class="grid grid-cols-3 gap-3">
<div>
<span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Qty</span>
<input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2 text-center" type="number" value="1"/>
</div>
<div>
<span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Price</span>
<input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2 text-right" type="number" value="2500"/>
</div>
<div class="text-right">
<span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Total</span>
<span class="text-sm font-bold text-[#111418] dark:text-white block h-10 flex items-center justify-end">$2500.00</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-gray-900/50 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
<div class="flex justify-between items-start mb-3">
<div class="flex-1">
<input class="w-full bg-transparent border-none p-0 text-sm font-semibold text-[#111418] dark:text-white focus:ring-0" value="Hosting (1 Year)"/>
</div>
<button class="text-red-500 p-1 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
<div class="grid grid-cols-3 gap-3">
<div>
<span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Qty</span>
<input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2 text-center" type="number" value="1"/>
</div>
<div>
<span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Price</span>
<input class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm p-2 text-right" type="number" value="300"/>
</div>
<div class="text-right">
<span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Total</span>
<span class="text-sm font-bold text-[#111418] dark:text-white block h-10 flex items-center justify-end">$300.00</span>
</div>
</div>
</div>
<button class="w-full flex items-center justify-center gap-2 py-3 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-primary font-bold text-sm hover:bg-primary/5 transition-colors">
<span class="material-symbols-outlined text-base">add_circle</span>
                            Add New Item
                        </button>
</section>
<section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800">
<label class="flex flex-col">
<span class="text-xs font-semibold pb-1.5 text-gray-600 dark:text-gray-400 uppercase tracking-wider">Notes / Terms</span>
<textarea class="form-textarea w-full rounded-lg text-[#111418] dark:text-gray-200 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 h-24 text-sm focus:ring-primary focus:border-primary p-3" placeholder="Add notes...">Payment is due within 30 days.</textarea>
</label>
</section>
<section class="bg-white dark:bg-gray-900/50 p-5 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-3">
<div class="flex justify-between items-center text-sm">
<span class="text-gray-500 dark:text-gray-400">Subtotal</span>
<span class="font-semibold text-[#111418] dark:text-white">$2,800.00</span>
</div>
<div class="flex justify-between items-center text-sm">
<span class="text-gray-500 dark:text-gray-400">Tax (0%)</span>
<span class="font-semibold text-[#111418] dark:text-white">$0.00</span>
</div>
<div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between items-center">
<span class="text-base font-bold text-[#111418] dark:text-white">Grand Total</span>
<span class="text-xl font-black text-primary">$2,800.00</span>
</div>
</section>
<div class="flex flex-col gap-3 pb-8">
<button class="w-full py-4 rounded-xl text-base font-bold bg-primary text-white hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                            Save Changes
                        </button>
<button class="w-full py-4 rounded-xl text-base font-semibold bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
</div>
</div>
</div>
</main>
<footer class="text-center py-6 px-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
<p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 uppercase tracking-widest">© 2023 Invoice Inc. | v1.2.0</p>
</footer>
</div>

</body></html>