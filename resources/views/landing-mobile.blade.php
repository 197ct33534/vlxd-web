<!DOCTYPE html>
<html class="light" lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
@include('partials.landing-meta', ['storeInfo' => $storeInfo ?? null])
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f97415",
                        "construction-orange": "#f97316",
                        "background-light": "#f8f7f5",
                        "background-dark": "#23170f",
                    },
                    fontFamily: {
                        "display": ["Work Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                    keyframes: {
                        'pulse-custom': {
                            '0%, 100%': { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(249, 115, 22, 0.7)' },
                            '50%': { transform: 'scale(1.05)', boxShadow: '0 0 0 10px rgba(249, 115, 22, 0)' },
                        },
                        'shake-custom': {
                            '0%, 100%': { transform: 'rotate(0deg)' },
                            '10%, 30%, 50%, 70%, 90%': { transform: 'rotate(-3deg)' },
                            '20%, 40%, 60%, 80%': { transform: 'rotate(3deg)' },
                        }
                    },
                    animation: {
                        'pulse-call': 'pulse-custom 2s infinite',
                        'shake-call': 'shake-custom 0.5s ease-in-out infinite alternate',
                    }
                },
            },
        }
    </script>
<style type="text/tailwindcss">
        @layer utilities {
            .animate-call-attention {
                animation: pulse-custom 2s infinite;
            }
            .shake-icon {
                animation: shake-custom 0.8s infinite;
            }
        }
        body { font-family: 'Work Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden" x-data="{ mobileMenu: false }" x-init="$watch('mobileMenu', v => { document.body.style.overflow = v ? 'hidden' : '' })">
@php
    $brandName = $storeInfo?->name ?? 'Vật Liệu Sáu Phụng';
    $phoneRaw = $storeInfo?->phone ?? '0909486474';
    $phoneTel = preg_replace('/\D+/', '', strtok($phoneRaw, '-,;|') ?: $phoneRaw) ?: '0909486474';
@endphp
<div class="max-w-[480px] mx-auto bg-white dark:bg-slate-900 min-h-screen shadow-2xl relative pb-24">
<header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 py-3 flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="bg-primary p-1.5 rounded-lg text-white">
<span class="material-symbols-outlined text-2xl">home_work</span>
</div>
<h1 class="font-bold text-lg tracking-tight uppercase">{{ $brandName }}</h1>
</div>
<button type="button" @click="mobileMenu = !mobileMenu" :aria-expanded="mobileMenu" aria-controls="mobile-nav-drawer" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 dark:bg-slate-800 dark:text-slate-200 active:scale-[0.98] transition-transform">
<span class="material-symbols-outlined shrink-0 text-xl">menu</span>
{{ __('landing.mobile_menu') }}
</button>
</header>

<!-- Menu trượt từ trái (hai lớp riêng để transition Alpine chạy đúng) -->
<div
    x-show="mobileMenu"
    x-cloak
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[60] bg-black/55 backdrop-blur-[3px]"
    id="mobile-nav-backdrop"
    @click="mobileMenu = false"
    aria-hidden="true"
></div>
<aside
    x-show="mobileMenu"
    x-cloak
    x-transition:enter="transition transform ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition transform ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed left-0 top-0 z-[61] flex h-full w-[min(20rem,88vw)] max-w-[20rem] flex-col bg-white shadow-2xl dark:bg-slate-900"
    id="mobile-nav-drawer"
    role="dialog"
    aria-modal="true"
    :aria-hidden="!mobileMenu"
    @click.stop
>
    <div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-4 dark:border-slate-800">
        <h2 class="text-sm font-black uppercase tracking-wider text-primary">{{ __('landing.mobile_menu_title') }}</h2>
        <button type="button" @click="mobileMenu = false" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200 active:scale-95 transition-transform">
            <span class="material-symbols-outlined">close</span>
            <span class="sr-only">{{ __('common.close') }}</span>
        </button>
    </div>
    <nav class="flex min-h-0 flex-1 flex-col gap-1 overflow-y-auto overscroll-contain px-3 py-4" aria-label="{{ __('landing.mobile_menu_title') }}">
        <a href="{{ url('/#danh-muc') }}" @click="mobileMenu = false" class="rounded-xl px-4 py-3.5 text-[15px] font-semibold text-slate-800 transition-colors hover:bg-primary/10 hover:text-primary dark:text-slate-100 dark:hover:bg-slate-800">{{ __('landing.nav_products') }}</a>
        <a href="{{ url('/#bao-gia') }}" @click="mobileMenu = false" class="rounded-xl px-4 py-3.5 text-[15px] font-semibold text-slate-800 transition-colors hover:bg-primary/10 hover:text-primary dark:text-slate-100 dark:hover:bg-slate-800">{{ __('landing.nav_pricing') }}</a>
        <a href="{{ url('/#lien-he') }}" @click="mobileMenu = false" class="rounded-xl px-4 py-3.5 text-[15px] font-semibold text-slate-800 transition-colors hover:bg-primary/10 hover:text-primary dark:text-slate-100 dark:hover:bg-slate-800">{{ __('landing.nav_contact') }}</a>
        <div class="my-2 border-t border-slate-100 dark:border-slate-800"></div>
        <a href="{{ route('dashboard') }}" @click="mobileMenu = false" class="mt-auto flex shrink-0 items-center justify-between gap-2 rounded-xl bg-primary/10 px-4 py-4 text-[15px] font-black text-primary">
            {{ __('landing.nav_dashboard') }}
            <span class="material-symbols-outlined shrink-0">dashboard</span>
        </a>
    </nav>
</aside>
<section id="danh-muc" class="scroll-mt-24 px-4 py-6">
<div class="relative w-full aspect-[4/5] rounded-xl overflow-hidden mb-6">
<img alt="Construction site materials" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1597974380476-fbf652dfe188?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NDAzfHxjb25zdHJ1Y3Rpb258ZW58MHx8MHx8fDA%3D"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-6">
<div class="bg-primary/20 backdrop-blur-md self-start px-3 py-1 rounded-full border border-primary/30 mb-3">
<span class="text-primary text-xs font-bold uppercase tracking-wider">Hơn 1000+ Nhà thầu tin dùng</span>
</div>
<h2 class="text-white text-3xl font-black leading-tight mb-4">
                        Cung Cấp Vật Liệu Xây Dựng Tận Chân Công Trình
                    </h2>
</div>
</div>
<div class="bg-slate-50 dark:bg-slate-800 p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<p class="text-sm font-medium mb-3 text-slate-600 dark:text-slate-400">{{ __('landing.quote_card_intro') }}</p>
@include('partials.landing-quote-form', ['variant' => 'card'])
</div>
</section>
<section class="px-4 py-8">
<div class="flex justify-between items-end mb-6">
<h3 class="text-xl font-bold">Danh Mục Sản Phẩm</h3>
<span class="text-xs font-medium text-slate-400">{{ __('landing.categories_hint') }}</span>
</div>
<div class="grid grid-cols-2 gap-3">
<div class="group">
<div class="aspect-square rounded-xl overflow-hidden mb-2 bg-slate-100">
<img alt="Xi măng" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwBHFZ4LiHKI34t1LA7RTjT5Of4hDZHFoxyIkYwKPcM7QeRWtoHZIMeIOq2KuY0jAtkOE9_3-41cUQp9mwwd92ax61UUiR3HOxOnF6rV1RDlmvfH8og8Ej6vJubR-Jia8p7SisnqT18tOgmtWuLR7hjKyl4nbLUTsPwh_oVcYSQ6V-hmtDtU5OREQKjmv8bYOQVifAODWjpQQ4FwtKSG3yQhNArPw8jT4YIMb-eDcZgjeo1Xj4V4xHSTod9QofLh-9IpwSIYqIxJA"/>
</div>
<h4 class="font-bold text-sm">Xi măng</h4>
<p class="text-slate-400 text-[10px]">Hà Tiên, Holcim...</p>
</div>
<div class="group">
<div class="aspect-square rounded-xl overflow-hidden mb-2 bg-slate-100">
<img alt="Sắt thép" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAL2Xmktg48vOeBlDicnVAQejbuptHGp7mdz8oZvNV4SrxLW1ijTLaF-NHlVfRCXj-8UYU4SkUME39GS3xNSaAX6WzSCUEgVFsJe3PrnqAwWfml-gJHuoIE7Nd9YMhwNJcCXpgCh5-_4JYh5_6BfRhG3FcmDibv4FeNwU-vbu1rTyVUB70YvmFSX1z4gZd79ZaDOOSdNPna9WOPs8mfWQQhEqZzZYUPt95mkveyKvDJos0QGkTfcdpQgA1kdB8pKn0JqziK_I0u-2E"/>
</div>
<h4 class="font-bold text-sm">Sắt thép</h4>
<p class="text-slate-400 text-[10px]">Hòa Phát, Pomina...</p>
</div>
<div class="group">
<div class="aspect-square rounded-xl overflow-hidden mb-2 bg-slate-100">
<img alt="Cát xây dựng" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqb6O3Uiovu0Zh7BcvqP2YWXnykvEIsWNT0CLmXXyRhkbkJYWB74kQ-JezBGhYyGQU4YFpH7tDKurLJcvrdh3W60l19r86OBzFuofw4NyA_FKb6DkUNMivIt_i4X2pi6r4uggMku2Y6JFS7olo_NteR0oi7TRFhN96Q8_XoCOfwD6quVNdmTN9sedABFDDIkyZj1JTQvsgbvNXskJEFsIwWM1wo_L70Nzm0vsl-SNdkQNiZjEdP4pRG3SFe8c3XY7kdhvwXxMElCQ"/>
</div>
<h4 class="font-bold text-sm">Cát xây dựng</h4>
<p class="text-slate-400 text-[10px]">Xây tô, bê tông...</p>
</div>
<div class="group">
<div class="aspect-square rounded-xl overflow-hidden mb-2 bg-slate-100">
<img alt="Gạch ống" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWy9wSfi53WILjvFmD0YusT7k7RRmTBrrtxoUi8R_KnevLpgKWd7CQGXi_rpa1YUbb5sr0DQOmZMBgyOAevYd_XU4MjIWvP3HzSjTypO0yhsoqYRWF9zfM-iv4R5KPm7cKgrlw9iVndbnLggfN3ZKfpYJbg81hh1cnCk1scuWP53bNtcY12CtHSR440oKeO6ZK1cRBGeCKaSQMETFv5GuswMf2GsXhzHoafRNefH16bnSqwLWlEk0dIzDTuCRzvTlUyyh9Kby-43o"/>
</div>
<h4 class="font-bold text-sm">Gạch ống</h4>
<p class="text-slate-400 text-[10px]">Gạch Tuynel...</p>
</div>
<div class="group">
<div class="aspect-square rounded-xl overflow-hidden mb-2 bg-slate-100">
<img alt="Đá xây dựng" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBXzkwrTbZSL_ow5SX1i70H6Su30b-GTPYZNl1u14eWvnnnkAn2BSI4ukmisRD-8T6hbqO59adwE6-WCQqwiEzWz29WN_8s6MZDj4yrMgvK5YyKzxmj5EEXOSD70jKgvQYEga6y72487IZYd7P_9pp3Fb8qorwIvKrRD1fD3hcTsB9RiNUy8QTkPzfziBOXlS1dy4Ud4YGLIL9mRabr5HXMx-HPTXyHNDUgHtEU4FKKWdSx7QjaETJnBolJrbJZ6vlXKWGYrQ3zkdQ"/>
</div>
<h4 class="font-bold text-sm">Đá xây dựng</h4>
<p class="text-slate-400 text-[10px]">Đá 1x2, 4x6...</p>
</div>
<div class="group">
<div class="aspect-square rounded-xl overflow-hidden mb-2 bg-slate-100">
<img alt="Xe cuốc" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1580901368855-059c4792ff22?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fGV4Y2F2YXRvcnxlbnwwfHwwfHx8MA%3D%3D"/>
</div>
<h4 class="font-bold text-sm">Xe cuốc</h4>
<p class="text-slate-400 text-[10px]">Xe cuốc, xe tải...</p>
</div>
</div>
</section>
<section class="bg-slate-50 dark:bg-slate-800/50 px-4 py-8">
<h3 class="text-xl font-bold mb-6 text-center">Tại sao nhà thầu tin chọn chúng tôi?</h3>
<div class="space-y-4">
<div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary">local_shipping</span>
</div>
<div>
<h4 class="font-bold text-base">Giao hàng 2h</h4>
<p class="text-sm text-slate-500">Đội ngũ vận tải hùng hậu, cam kết giao hàng nhanh nhất nội thành.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary">payments</span>
</div>
<div>
<h4 class="font-bold text-base">Giá sỉ cạnh tranh</h4>
<p class="text-sm text-slate-500">Chiết khấu cực tốt cho nhà thầu và các đại lý cấp 2.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary">verified</span>
</div>
<div>
<h4 class="font-bold text-base">Chất lượng kiểm định</h4>
<p class="text-sm text-slate-500">100% hàng chính hãng, đầy đủ CO/CQ từ nhà máy.</p>
</div>
</div>
<div class="flex items-start gap-4 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary">support_agent</span>
</div>
<div>
<h4 class="font-bold text-base">Hỗ trợ 24/7</h4>
<p class="text-sm text-slate-500">Tư vấn kỹ thuật và xử lý đơn hàng mọi lúc bạn cần.</p>
</div>
</div>
</div>
</section>
<section id="bao-gia" class="scroll-mt-24 px-4 py-10">
<div class="mb-5">
<h2 class="text-2xl font-black leading-tight text-slate-900 dark:text-white">{{ __('landing.mobile_pricing_heading') }}</h2>
<p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('landing.mobile_pricing_updated', ['time' => date('H:i'), 'date' => date('d/m/Y')]) }}</p>
</div>
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/80">
<div class="overflow-x-auto hide-scrollbar">
<table class="w-full min-w-[300px] border-collapse text-left">
<thead>
<tr class="bg-primary text-white">
<th class="px-4 py-3.5 text-xs font-bold uppercase tracking-wide">{{ __('landing.pricing_col_material') }}</th>
<th class="px-3 py-3.5 text-center text-xs font-bold uppercase tracking-wide">{{ __('landing.pricing_col_unit') }}</th>
<th class="px-4 py-3.5 text-right text-xs font-bold uppercase tracking-wide">{{ __('landing.pricing_col_price') }}</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-700">
@forelse($latestPrices->take(8) as $item)
<tr class="bg-white odd:bg-slate-50/80 dark:bg-slate-800/50 dark:odd:bg-slate-800">
<td class="max-w-[10rem] px-4 py-3.5 text-sm font-semibold leading-snug text-slate-900 dark:text-slate-100">{{ $item->name }}</td>
<td class="whitespace-nowrap px-2 py-3.5 text-center text-sm text-slate-600 dark:text-slate-300">{{ $item->unit }}</td>
<td class="whitespace-nowrap px-4 py-3.5 text-right text-sm font-bold text-primary">{{ number_format($item->price) }}đ</td>
</tr>
@empty
<tr>
<td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500 italic">{{ __('landing.pricing_empty') }}</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
<p class="mt-4 text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ __('landing.pricing_footnote') }}</p>
<a href="{{ route('quotation.download') }}" class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3.5 text-sm font-bold text-white shadow-lg shadow-primary/25 transition-transform active:scale-[0.99]">
<span class="material-symbols-outlined text-[1.25rem]">download</span>
{{ __('landing.pricing_download') }}
</a>
</section>
<section class="px-4 py-8 bg-slate-900 text-white rounded-t-3xl mt-4">
<h3 class="text-xl font-bold mb-8 text-center">Quy Trình Đặt Hàng</h3>
<div class="relative pl-8 space-y-10">
<div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-primary/30"></div>
<div class="relative">
<div class="absolute -left-[30px] top-0 w-8 h-8 rounded-full bg-primary border-4 border-slate-900 flex items-center justify-center font-bold text-xs">1</div>
<h4 class="font-bold text-lg leading-none mb-1">Liên Hệ</h4>
<p class="text-slate-400 text-sm">Gửi yêu cầu qua Zalo hoặc Hotline</p>
</div>
<div class="relative">
<div class="absolute -left-[30px] top-0 w-8 h-8 rounded-full bg-primary border-4 border-slate-900 flex items-center justify-center font-bold text-xs">2</div>
<h4 class="font-bold text-lg leading-none mb-1">Báo Giá</h4>
<p class="text-slate-400 text-sm">Nhận báo giá chi tiết trong 15 phút</p>
</div>
<div class="relative">
<div class="absolute -left-[30px] top-0 w-8 h-8 rounded-full bg-primary border-4 border-slate-900 flex items-center justify-center font-bold text-xs">3</div>
<h4 class="font-bold text-lg leading-none mb-1">Giao Hàng</h4>
<p class="text-slate-400 text-sm">Vận chuyển vật liệu đến công trình</p>
</div>
<div class="relative">
<div class="absolute -left-[30px] top-0 w-8 h-8 rounded-full bg-primary border-4 border-slate-900 flex items-center justify-center font-bold text-xs">4</div>
<h4 class="font-bold text-lg leading-none mb-1">Thanh Toán</h4>
<p class="text-slate-400 text-sm">Kiểm tra đủ số lượng và thanh toán</p>
</div>
</div>
</section>
<section class="px-4 py-8 overflow-hidden">
<h3 class="text-xl font-bold mb-6">Đối Tác Nói Gì?</h3>
<div class="flex gap-4 overflow-x-auto hide-scrollbar pb-4 -mx-4 px-4 snap-x">
    <!-- Testimonial 1 -->
    <div class="min-w-[280px] bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl snap-center border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex gap-1 mb-3 text-primary">
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
        </div>
        <p class="text-sm italic mb-4">"Đội xe giao hàng của Sáu Phụng rất chuyên nghiệp, lùi xe vào hẻm rất khéo. Hàng giao đúng hẹn."</p>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                N
            </div>
            <div>
                <p class="font-bold text-xs">Anh Nam</p>
                <p class="text-[10px] text-slate-500 uppercase">Thầu Xây Dựng Q.2</p>
            </div>
        </div>
    </div>
    <!-- Testimonial 2 -->
    <div class="min-w-[280px] bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl snap-center border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex gap-1 mb-3 text-primary">
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
        </div>
        <p class="text-sm italic mb-4">"Giá sắt thép ở đây luôn ổn định và tốt. Hóa đơn chứng từ rõ ràng, làm việc rất chuyên nghiệp."</p>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
                H
            </div>
            <div>
                <p class="font-bold text-xs">Chị Hạnh</p>
                <p class="text-[10px] text-slate-500 uppercase">Gia Định Cons</p>
            </div>
        </div>
    </div>
    <!-- Testimonial 3 -->
    <div class="min-w-[280px] bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl snap-center border border-slate-100 dark:border-slate-700 shadow-sm">
        <div class="flex gap-1 mb-3 text-primary">
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
            <span class="material-symbols-outlined text-sm fill-1">star</span>
        </div>
        <p class="text-sm italic mb-4">"Cung ứng vật liệu số lượng lớn rất nhanh. Sáu Phụng giúp chúng tôi kịp tiến độ bàn giao công trình."</p>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-sm">
                T
            </div>
            <div>
                <p class="font-bold text-xs">Anh Thành</p>
                <p class="text-[10px] text-slate-500 uppercase">Kỹ Sư VinHomes</p>
            </div>
        </div>
    </div>
</div>
</section>
<footer id="lien-he" class="scroll-mt-24 border-t border-slate-200 bg-slate-100 px-4 py-10 dark:border-slate-800 dark:bg-slate-900">
<div class="mb-8">
<h4 class="font-bold mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-primary">location_on</span>
                    Địa Chỉ Kho Bãi
                </h4>
<div class="w-full h-40 bg-slate-200 rounded-xl overflow-hidden grayscale mb-4">
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d244.96130390958572!2d106.7763743!3d10.7821139!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175242a2ac96513%3A0x5a1d03abef56d69d!2zVmx4ZCBTw6F1IFBo4bulbmcgR-G6oWNo!5e0!3m2!1svi!2s!4v1771669301077!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
<p class="text-sm text-slate-500">{{ $storeInfo?->address ?? 'Đường Số 6, Bình Trưng Đông, Thủ Đức, TP. Hồ Chí Minh' }}</p>
@if($storeInfo && ($storeInfo->bank_name || $storeInfo->bank_account))
<p class="text-xs text-slate-500 mt-2">
    @if($storeInfo->bank_name){{ $storeInfo->bank_name }}@endif
    @if($storeInfo->bank_account) — STK: {{ $storeInfo->bank_account }}@endif
</p>
@endif
</div>
<div class="text-center text-[10px] text-slate-400">
                © {{ date('Y') }} {{ $brandName }}.
            </div>
</footer>
<div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-[480px] bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 p-3 grid grid-cols-2 gap-3 z-50">
<a class="flex items-center justify-center gap-2 bg-construction-orange text-white font-bold py-3.5 rounded-lg active:scale-95 transition-transform animate-call-attention" href="tel:{{ $phoneTel }}">
<span class="material-symbols-outlined shake-icon">call</span>
                GỌI NGAY
            </a>
<a class="flex items-center justify-center gap-2 bg-[#0068ff] text-white font-bold py-3.5 rounded-lg active:scale-95 transition-transform" href="https://zalo.me/{{ $phoneTel }}">
<span class="material-symbols-outlined">chat</span>
                ZALO ME
            </a>
</div>
</div>

</body></html>