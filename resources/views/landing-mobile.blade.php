<!DOCTYPE html>
<html class="light" lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Vật Liệu Xây Dựng - Landing Page</title>
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
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden" x-data="{ mobileMenu: false }">
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
<button type="button" @click="mobileMenu = !mobileMenu" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2 py-2 dark:bg-slate-800">
<span class="material-symbols-outlined shrink-0">menu</span>
<span class="max-w-[6rem] truncate text-xs font-semibold"></span>
</button>
</header>

<!-- Mobile Menu Overlay -->
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-[60]">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="mobileMenu = false"></div>
    <div class="absolute right-0 top-0 bottom-0 w-64 bg-white dark:bg-slate-900 p-6 shadow-2xl transition-transform duration-300"
         x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="translate-x-full">
        <div class="flex justify-between items-center mb-8">
            <h2 class="font-bold text-primary">MENU</h2>
            <button type="button" @click="mobileMenu = false" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2 py-2 dark:bg-slate-800">
                <span class="material-symbols-outlined shrink-0">close</span>
                <span class="max-w-[5rem] truncate text-xs font-bold">{{ __('common.close') }}</span>
            </button>
        </div>
        <nav class="flex flex-col gap-4">
            <a href="{{ url('/#danh-muc') }}" class="p-3 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors">Sản phẩm</a>
            <a href="{{ url('/#bao-gia') }}" class="p-3 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors">Báo giá</a>
            <a href="{{ url('/#lien-he') }}" class="p-3 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors">Liên hệ</a>
            <a href="{{ route('login') }}" class="p-3 font-bold hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-colors">Đăng nhập</a>
            <a href="{{ route('dashboard') }}" class="p-4 font-black bg-primary/10 text-primary rounded-xl flex items-center justify-between">
                Vào Dashboard
                <span class="material-symbols-outlined">dashboard</span>
            </a>
        </nav>
    </div>
</div>
<section id="danh-muc" class="px-4 py-6 scroll-mt-20">
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
<p class="text-sm font-medium mb-3 text-slate-600 dark:text-slate-400">Nhận báo giá cạnh tranh nhất ngay hôm nay</p>
<div class="flex flex-col gap-3">
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">phone_iphone</span>
<input class="w-full pl-10 pr-4 py-3.5 rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-900 focus:ring-primary focus:border-primary" placeholder="Số điện thoại của bạn" type="tel"/>
</div>
<button class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-4 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
<span>NHẬN BÁO GIÁ NGAY</span>
<span class="material-symbols-outlined text-sm">arrow_forward</span>
</button>
</div>
</div>
</section>
<section class="px-4 py-8">
<div class="flex justify-between items-end mb-6">
<h3 class="text-xl font-bold">Danh Mục Sản Phẩm</h3>
<a class="text-primary text-sm font-semibold" href="#">Tất cả</a>
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
<section id="bao-gia" class="px-4 py-8 scroll-mt-20">
    <h3 class="text-xl font-bold mb-1">Bảng Giá Tham Khảo</h3>
    <p class="text-[10px] text-slate-500 mb-4 italic">Cập nhật: {{ date('H:i - d/m/Y') }}</p>
<div class="overflow-x-auto hide-scrollbar -mx-4 px-4">
<table class="w-full text-left border-collapse min-w-[320px]">
<thead>
<tr class="border-b-2 border-slate-100 dark:border-slate-800">
<th class="py-3 font-bold text-sm text-slate-400">Vật Liệu</th>
<th class="py-3 font-bold text-sm text-slate-400 text-center">Đơn Vị</th>
<th class="py-3 font-bold text-sm text-slate-400 text-right">Đơn Giá</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
@forelse($latestPrices->take(5) as $item)
<tr>
<td class="py-4 font-semibold text-sm">{{ $item->name }}</td>
<td class="py-4 text-center text-sm">{{ $item->unit }}</td>
<td class="py-4 text-right font-bold text-primary">{{ number_format($item->price) }}đ</td>
</tr>
@empty
<tr>
<td colspan="3" class="py-8 text-center text-slate-400 italic text-xs">Đang cập nhật giá mới nhất...</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<p class="text-[10px] text-slate-400 mt-4 italic">* Lưu ý: Giá có thể thay đổi tùy theo vị trí công trình và khối lượng.</p>
<div class="mt-6">
    <a href="{{ route('quotation.download') }}" class="w-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold py-3 rounded-lg flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-700">
        <span class="material-symbols-outlined text-sm">download</span>
        TẢI BÁO GIÁ CHI TIẾT
    </a>
</div>
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
<footer id="lien-he" class="px-4 py-10 bg-slate-100 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 scroll-mt-20">
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