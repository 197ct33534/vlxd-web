<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Invoice Detail - CMS Admin</title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
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
              "display": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            vertical-align: middle;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased font-display">
<!-- Main Container (Simulating Mobile View on Desktop) -->
<div class="max-w-md mx-auto min-h-screen bg-white dark:bg-slate-900 shadow-xl flex flex-col relative pb-32">
<!-- Top Navigation -->
<header class="sticky top-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 py-3 flex items-center justify-between">
<div class="flex items-center gap-3">
<button class="p-2 -ml-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
<span class="material-symbols-outlined text-slate-600 dark:text-slate-400">arrow_back</span>
</button>
<h1 class="text-lg font-bold tracking-tight">Invoice Details</h1>
</div>
<button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full">
<span class="material-symbols-outlined text-slate-600 dark:text-slate-400">more_vert</span>
</button>
</header>
<!-- Main Content Scroll Area -->
<main class="flex-1 overflow-y-auto">
<!-- Header Section: ID & Status -->
<section class="p-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
<div class="flex justify-between items-start mb-4">
<div>
<p class="text-sm font-medium text-primary mb-1 uppercase tracking-wider">Reference</p>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">#INV-2023-001</h2>
</div>
<span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-sm font-bold border border-emerald-200 dark:border-emerald-800">
                        Paid
                    </span>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="flex flex-col">
<span class="text-xs text-slate-500 dark:text-slate-400 uppercase font-semibold">Issued Date</span>
<span class="text-sm font-medium">Oct 24, 2023</span>
</div>
<div class="flex flex-col text-right">
<span class="text-xs text-slate-500 dark:text-slate-400 uppercase font-semibold">Due Date</span>
<span class="text-sm font-medium">Nov 24, 2023</span>
</div>
</div>
</section>
<!-- Info Cards Section -->
<section class="p-4 space-y-4">
<!-- Customer Card -->
<div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
<div class="flex items-center gap-2 mb-3">
<span class="material-symbols-outlined text-primary text-xl">person</span>
<h3 class="font-bold text-slate-700 dark:text-slate-300">Bill To</h3>
</div>
<p class="text-base font-bold text-slate-900 dark:text-white">Acme Corporation</p>
<p class="text-sm text-slate-500 dark:text-slate-400">alex@acmecorp.com</p>
<p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                        123 Business Way, Suite 500<br/>
                        San Francisco, CA 94107
                    </p>
</div>
<!-- Project Card -->
<div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
<div class="flex items-center gap-2 mb-3">
<span class="material-symbols-outlined text-primary text-xl">assignment</span>
<h3 class="font-bold text-slate-700 dark:text-slate-300">Project Details</h3>
</div>
<div class="flex justify-between items-center">
<div>
<p class="text-base font-bold text-slate-900 dark:text-white">Website Redesign</p>
<p class="text-sm text-slate-500 dark:text-slate-400">Milestone: Phase 1 (UI/UX)</p>
</div>
<div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
<span class="material-symbols-outlined">web</span>
</div>
</div>
</div>
</section>
<!-- Line Items List -->
<section class="px-4 py-2">
<div class="flex items-center justify-between mb-4 px-2">
<h3 class="text-lg font-bold text-slate-900 dark:text-white">Line Items</h3>
<span class="text-xs font-medium text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded">3 Items</span>
</div>
<div class="space-y-3">
<!-- Item 1 -->
<div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<div class="flex justify-between items-start mb-2">
<h4 class="font-semibold text-slate-900 dark:text-white pr-4">UI Design - Landing Page</h4>
<span class="font-bold text-slate-900 dark:text-white">$1,200.00</span>
</div>
<div class="flex justify-between items-center text-sm text-slate-500 dark:text-slate-400">
<span>Qty: 1</span>
<span>$1,200.00 / unit</span>
</div>
</div>
<!-- Item 2 -->
<div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<div class="flex justify-between items-start mb-2">
<h4 class="font-semibold text-slate-900 dark:text-white pr-4">Custom Icon Set</h4>
<span class="font-bold text-slate-900 dark:text-white">$450.00</span>
</div>
<div class="flex justify-between items-center text-sm text-slate-500 dark:text-slate-400">
<span>Qty: 15</span>
<span>$30.00 / unit</span>
</div>
</div>
<!-- Item 3 -->
<div class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<div class="flex justify-between items-start mb-2">
<h4 class="font-semibold text-slate-900 dark:text-white pr-4">Brand Strategy Workshop</h4>
<span class="font-bold text-slate-900 dark:text-white">$800.00</span>
</div>
<div class="flex justify-between items-center text-sm text-slate-500 dark:text-slate-400">
<span>Qty: 2</span>
<span>$400.00 / unit</span>
</div>
</div>
</div>
</section>
<!-- Financial Summary -->
<section class="p-6 mt-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
<div class="space-y-3">
<div class="flex justify-between text-sm">
<span class="text-slate-500 dark:text-slate-400">Subtotal</span>
<span class="font-medium">$2,450.00</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500 dark:text-slate-400">Tax (10%)</span>
<span class="font-medium">$245.00</span>
</div>
<div class="flex justify-between text-sm">
<span class="text-slate-500 dark:text-slate-400">Discount (Promo)</span>
<span class="font-medium text-emerald-600 dark:text-emerald-400">-$100.00</span>
</div>
<div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
<span class="text-lg font-bold text-slate-900 dark:text-white">Total Amount</span>
<span class="text-2xl font-black text-primary">$2,595.00</span>
</div>
</div>
</section>
<!-- Map/Location Placeholder -->
<div class="px-4 pb-12">
<div class="w-full h-32 bg-slate-200 dark:bg-slate-800 rounded-xl relative overflow-hidden">
<img class="w-full h-full object-cover opacity-50" data-alt="Stylized map showing business location" data-location="San Francisco" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7a2jr0OE0itWh1IfzFziasN6KqHEK6jdCItoWrbzzEPJsIE3Kay-d-e4tfOKau7Hohtg8j-JEGjuUr9Twu6p-dO02pDKda_9oAHnn5N90dkV6hP6r8zPgbrMw-Zd5ngJsHw3jxsLYXzfElI8FhQYseHVChOnyiDdz4LxyLBWvesh8I9AHWpnIWTSi2rj1n9ceAuGg0SB6qNk1ptOkFy--vPkJgFqu8BvFR4WzeqJIWK2Iq8MIV6tLtV3M5KVXlFVGcI7_TKoJ4Yg"/>
<div class="absolute inset-0 flex items-center justify-center">
<div class="bg-white dark:bg-slate-900 px-3 py-1 rounded-full shadow-lg border border-slate-200 dark:border-slate-700 flex items-center gap-1">
<span class="material-symbols-outlined text-primary text-sm">location_on</span>
<span class="text-xs font-bold">Billing Office</span>
</div>
</div>
</div>
</div>
</main>
<!-- Sticky Bottom Bar Actions -->
<footer class="absolute bottom-0 left-0 right-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-4 pb-8 z-30">
<div class="grid grid-cols-2 gap-3 max-w-md mx-auto">
<button class="flex items-center justify-center gap-2 h-12 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-slate-100 font-bold rounded-xl active:scale-[0.98] transition-all">
<span class="material-symbols-outlined text-[20px]">print</span>
<span>Print</span>
</button>
<button class="flex items-center justify-center gap-2 h-12 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/25 active:scale-[0.98] transition-all">
<span class="material-symbols-outlined text-[20px]">download</span>
<span>Download PDF</span>
</button>
</div>
</footer>
</div>
</body></html>