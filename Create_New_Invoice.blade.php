        <!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Create Invoice - Mobile View</title>
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
<style type="text/tailwindcss">
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }@media (max-width: 768px) {
            .mobile-hide { display: none; }
            .sticky-actions {
                position: sticky;
                bottom: 0;
                background: white;
                padding: 1rem;
                box-shadow: 0 -4px 6px -1px rgb(0 0 0 / 0.1);
                z-index: 40;
            }
        }
    </style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark min-h-screen flex flex-col">
<header class="bg-white dark:bg-background-dark border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 h-16 sticky top-0 z-50">
<div class="flex items-center gap-3">
<button class="p-2 -ml-2 text-gray-600 dark:text-gray-300">
<span class="material-symbols-outlined text-2xl">menu</span>
</button>
<div class="bg-primary rounded-lg p-1.5 text-white">
<span class="material-symbols-outlined text-xl">receipt_long</span>
</div>
<h1 class="text-lg font-bold text-gray-800 dark:text-white">Invoice Inc.</h1>
</div>
<div class="flex items-center gap-2">
<button class="p-2 text-gray-600 dark:text-gray-400">
<span class="material-symbols-outlined text-2xl">search</span>
</button>
<button class="p-2 text-gray-600 dark:text-gray-400 relative">
<span class="material-symbols-outlined text-2xl">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
</button>
<img class="w-8 h-8 rounded-full object-cover ml-1" data-alt="User Avatar" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCecDTqUCm4JfCfK7EsJ-vavmeOyFgREdnY3D00vK0f2bZ9Je9KGutFWq-UUyv8YtrcvA0DwTHmlzrpxGyNOEBpCk9kd_FbqvTp7W7zTXbZkqJxv2Z2aVhI3-t23tKPfHn15czM-jNC7IsDmqE9Yjg_8PvpTE6qdTEWWTLCm8t4Wx04-FppM5TywvJY4mdgoW_4binJc-SCpQGcJBAA-LgltuCUyoNPHjegQyzoLVP_7Hta14h_eDnEmSc6-Mv2KHtNFod69I_j5m0"/>
</div>
</header>
<aside class="hidden lg:flex w-64 bg-white dark:bg-background-dark border-r border-gray-200 dark:border-gray-800 flex-col fixed inset-y-0 left-0 z-40">
</aside>
<main class="flex-1 pb-24 md:pb-8 p-4 md:p-8 max-w-2xl mx-auto w-full">
<div class="mb-6">
<h1 class="text-2xl font-bold text-gray-900 dark:text-white">New Invoice</h1>
<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Fill in the details to generate a new invoice.</p>
</div>
<div class="space-y-6">
<div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-5">
<label class="flex flex-col">
<span class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Customer</span>
<div class="relative">
<select class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 h-12 px-4 focus:ring-2 focus:ring-primary focus:border-primary appearance-none">
<option value="">Select a customer</option>
<option value="1">Global Tech Inc.</option>
<option value="2">Innovate Solutions</option>
<option value="3">Creative Minds LLC</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">expand_more</span>
</div>
</label>
<div>
<span class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 block">Invoice Status</span>
<div class="flex flex-col gap-2">
<label class="relative flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 group">
<span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-has-[:checked]:text-primary">Draft</span>
<input checked="" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary" name="status" type="radio"/>
</label>
<label class="relative flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 group">
<span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-has-[:checked]:text-primary">Sent</span>
<input class="w-4 h-4 text-primary border-gray-300 focus:ring-primary" name="status" type="radio"/>
</label>
<label class="relative flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 group">
<span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-has-[:checked]:text-primary">Paid</span>
<input class="w-4 h-4 text-primary border-gray-300 focus:ring-primary" name="status" type="radio"/>
</label>
</div>
</div>
</div>
<div class="space-y-4">
<div class="flex items-center justify-between px-1">
<h2 class="text-lg font-bold text-gray-900 dark:text-white">Invoice Items</h2>
<span class="text-xs font-medium text-gray-500 uppercase tracking-wider">2 Items</span>
</div>
<div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 relative">
<button class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
<div class="space-y-4">
<div>
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Description</label>
<input class="w-full border-0 p-0 text-base font-semibold text-gray-800 dark:text-white bg-transparent focus:ring-0" placeholder="Item name..." type="text" value="Web Design Services"/>
</div>
<div class="grid grid-cols-3 gap-4 border-t border-gray-100 dark:border-gray-700 pt-3">
<div>
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Qty</label>
<input class="w-full border-0 p-0 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" type="number" value="1"/>
</div>
<div>
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Price</label>
<input class="w-full border-0 p-0 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" type="number" value="2500"/>
</div>
<div class="text-right">
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Total</label>
<span class="text-sm font-bold text-primary">$2,500.00</span>
</div>
</div>
</div>
</div>
<div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 relative">
<button class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
<div class="space-y-4">
<div>
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Description</label>
<input class="w-full border-0 p-0 text-base font-semibold text-gray-800 dark:text-white bg-transparent focus:ring-0" placeholder="Item name..." type="text" value="Hosting (1 Year)"/>
</div>
<div class="grid grid-cols-3 gap-4 border-t border-gray-100 dark:border-gray-700 pt-3">
<div>
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Qty</label>
<input class="w-full border-0 p-0 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" type="number" value="1"/>
</div>
<div>
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Price</label>
<input class="w-full border-0 p-0 text-sm text-gray-700 dark:text-gray-300 bg-transparent focus:ring-0" type="number" value="300"/>
</div>
<div class="text-right">
<label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Total</label>
<span class="text-sm font-bold text-primary">$300.00</span>
</div>
</div>
</div>
</div>
<button class="w-full py-4 flex items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-primary font-bold hover:bg-primary/5 transition-colors">
<span class="material-symbols-outlined">add_circle</span>
                    Add New Item
                </button>
</div>
<div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-3">
<div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
<span>Subtotal</span>
<span class="font-medium">$2,800.00</span>
</div>
<div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
<span>Tax (10%)</span>
<span class="font-medium">$280.00</span>
</div>
<div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
<span>Discount</span>
<span class="font-medium text-green-600">-$0.00</span>
</div>
<div class="pt-3 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
<span class="text-base font-bold text-gray-900 dark:text-white">Grand Total</span>
<span class="text-xl font-black text-primary">$3,080.00</span>
</div>
</div>
</div>
</main>
<footer class="hidden md:block text-center py-6 text-xs text-gray-500 dark:text-gray-400">
        © 2023 Invoice Inc. All Rights Reserved. v1.0.0
    </footer>
<div class="sticky-actions flex gap-3 md:static md:bg-transparent md:shadow-none md:p-0 md:mt-8 md:justify-end md:max-w-2xl md:mx-auto md:w-full md:px-0">
<button class="flex-1 md:flex-none md:px-8 py-4 md:py-3 rounded-xl md:rounded-lg text-sm font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
            Cancel
        </button>
<button class="flex-[2] md:flex-none md:px-10 py-4 md:py-3 rounded-xl md:rounded-lg text-sm font-bold text-white bg-primary shadow-lg shadow-primary/25">
            Save Invoice
        </button>
</div>

</body></html>