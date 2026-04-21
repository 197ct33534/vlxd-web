<!DOCTYPE html>

<html lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Vật Liệu Xây Dựng - Uy Tín &amp; Giá Tốt</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#f97415",
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
                        "2xl": "2rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body { font-family: 'Work Sans', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 selection:bg-primary/30">
@php
    $brandName = $storeInfo?->name ?? 'Vật Liệu Sáu Phụng';
    $phoneRaw = $storeInfo?->phone ?? '0909.486.474 - 0967.847.582';
    $phoneTel = preg_replace('/\D+/', '', strtok($phoneRaw, '-,;|') ?: $phoneRaw) ?: '0909486474';
@endphp
<!-- Sticky Navigation -->
<header class="fixed top-0 left-0 right-0 z-50 glass-nav border-b border-primary/10">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex justify-between items-center h-20">
<div class="flex items-center gap-3">
<div class="bg-primary text-white p-2 rounded-lg">
<span class="material-symbols-outlined text-3xl">home_work</span>
</div>
<span class="text-2xl font-black tracking-tight text-slate-900 uppercase">{{ $brandName }}</span>
</div>
<nav class="hidden md:flex items-center gap-8">
<a class="text-sm font-semibold hover:text-primary transition-colors" href="{{ url('/#danh-muc') }}">Sản phẩm</a>
<a class="text-sm font-semibold hover:text-primary transition-colors" href="{{ url('/#bao-gia') }}">Báo giá</a>
<a class="text-sm font-semibold hover:text-primary transition-colors" href="{{ route('login') }}">Quản trị</a>
<a class="text-sm font-semibold hover:text-primary transition-colors" href="{{ url('/#lien-he') }}">Liên hệ</a>
</nav>
<div class="flex items-center gap-4">
<a href="{{ url('/#bao-gia') }}" class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-lg shadow-primary/20 inline-block">
                        Nhận báo giá
                    </a>
</div>
</div>
</div>
</header>
<!-- Hero Section -->
<section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
<div class="absolute inset-0 z-0">
<div class="absolute inset-0 bg-black/50 z-10"></div>
<img alt="Hero background" class="w-full h-full object-cover" data-alt="Modern construction site with cranes and sunset" src="https://images.unsplash.com/photo-1597974380476-fbf652dfe188?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NDAzfHxjb25zdHJ1Y3Rpb258ZW58MHx8MHx8fDA%3D"/>
</div>
<div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="max-w-3xl">
<h1 class="text-4xl md:text-6xl font-black text-white leading-tight mb-6">
                    Cung cấp vật liệu xây dựng <span class="text-primary">uy tín – giá tốt</span> mỗi ngày
                </h1>
<p class="text-lg text-slate-200 mb-10 max-w-xl">
                    Đơn vị cung ứng vật liệu hàng đầu cho các công trình trọng điểm. Giao hàng tận nơi trong 2 giờ, đầy đủ chứng chỉ chất lượng CO/CQ.
                </p>
<div class="bg-white/10 backdrop-blur-md p-2 rounded-2xl border border-white/20 max-w-lg shadow-2xl">
<div class="bg-white rounded-xl p-2 flex flex-col sm:flex-row gap-2">
<div class="flex-1 flex items-center px-4">
<span class="material-symbols-outlined text-slate-400 mr-2">phone_iphone</span>
<input class="w-full border-0 focus:ring-0 text-slate-900 font-medium placeholder:text-slate-400" placeholder="Nhập số điện thoại" type="tel"/>
</div>
<button class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-lg font-bold transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                            Nhận báo giá ngay
                            <span class="material-symbols-outlined">trending_flat</span>
</button>
</div>
</div>
<div class="mt-6 flex items-center gap-6 text-white/80">
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-primary text-sm">check_circle</span>
<span class="text-xs font-medium uppercase tracking-wider">Hơn 5000+ Khách hàng</span>
</div>
<div class="flex items-center gap-1.5">
<span class="material-symbols-outlined text-primary text-sm">check_circle</span>
<span class="text-xs font-medium uppercase tracking-wider">Giao nhanh 2h</span>
</div>
</div>
</div>
</div>
</section>
<!-- Product Categories -->
<section id="danh-muc" class="py-24 bg-background-light dark:bg-background-dark scroll-mt-24">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-4">Danh mục sản phẩm</h2>
<div class="h-1.5 w-24 bg-primary mx-auto rounded-full"></div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
<!-- Category Cards -->
<div class="group relative overflow-hidden rounded-2xl aspect-[4/3] cursor-pointer shadow-lg">
<img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Stacks of construction cement bags" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwBHFZ4LiHKI34t1LA7RTjT5Of4hDZHFoxyIkYwKPcM7QeRWtoHZIMeIOq2KuY0jAtkOE9_3-41cUQp9mwwd92ax61UUiR3HOxOnF6rV1RDlmvfH8og8Ej6vJubR-Jia8p7SisnqT18tOgmtWuLR7hjKyl4nbLUTsPwh_oVcYSQ6V-hmtDtU5OREQKjmv8bYOQVifAODWjpQQ4FwtKSG3yQhNArPw8jT4YIMb-eDcZgjeo1Xj4V4xHSTod9QofLh-9IpwSIYqIxJA"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
<div class="absolute bottom-6 left-6">
<h3 class="text-2xl font-bold text-white mb-1">Xi măng</h3>
<p class="text-slate-300 text-sm">Hà Tiên, Holcim, Nghi Sơn...</p>
</div>
<div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="bg-primary text-white p-2 rounded-full material-symbols-outlined">north_east</span>
</div>
</div>
<div class="group relative overflow-hidden rounded-2xl aspect-[4/3] cursor-pointer shadow-lg">
<img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Industrial steel rebar for construction" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAL2Xmktg48vOeBlDicnVAQejbuptHGp7mdz8oZvNV4SrxLW1ijTLaF-NHlVfRCXj-8UYU4SkUME39GS3xNSaAX6WzSCUEgVFsJe3PrnqAwWfml-gJHuoIE7Nd9YMhwNJcCXpgCh5-_4JYh5_6BfRhG3FcmDibv4FeNwU-vbu1rTyVUB70YvmFSX1z4gZd79ZaDOOSdNPna9WOPs8mfWQQhEqZzZYUPt95mkveyKvDJos0QGkTfcdpQgA1kdB8pKn0JqziK_I0u-2E"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
<div class="absolute bottom-6 left-6">
<h3 class="text-2xl font-bold text-white mb-1">Sắt thép</h3>
<p class="text-slate-300 text-sm">Hòa Phát, Pomina, Miền Nam...</p>
</div>
<div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="bg-primary text-white p-2 rounded-full material-symbols-outlined">north_east</span>
</div>
</div>
<div class="group relative overflow-hidden rounded-2xl aspect-[4/3] cursor-pointer shadow-lg">
<img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Pile of high quality construction sand" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAqb6O3Uiovu0Zh7BcvqP2YWXnykvEIsWNT0CLmXXyRhkbkJYWB74kQ-JezBGhYyGQU4YFpH7tDKurLJcvrdh3W60l19r86OBzFuofw4NyA_FKb6DkUNMivIt_i4X2pi6r4uggMku2Y6JFS7olo_NteR0oi7TRFhN96Q8_XoCOfwD6quVNdmTN9sedABFDDIkyZj1JTQvsgbvNXskJEFsIwWM1wo_L70Nzm0vsl-SNdkQNiZjEdP4pRG3SFe8c3XY7kdhvwXxMElCQ"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
<div class="absolute bottom-6 left-6">
<h3 class="text-2xl font-bold text-white mb-1">Cát xây dựng</h3>
<p class="text-slate-300 text-sm">Cát xây tô, cát bê tông, cát san lấp</p>
</div>
<div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="bg-primary text-white p-2 rounded-full material-symbols-outlined">north_east</span>
</div>
</div>
<div class="group relative overflow-hidden rounded-2xl aspect-[4/3] cursor-pointer shadow-lg">
<img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Red clay bricks stacked neatly" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWy9wSfi53WILjvFmD0YusT7k7RRmTBrrtxoUi8R_KnevLpgKWd7CQGXi_rpa1YUbb5sr0DQOmZMBgyOAevYd_XU4MjIWvP3HzSjTypO0yhsoqYRWF9zfM-iv4R5KPm7cKgrlw9iVndbnLggfN3ZKfpYJbg81hh1cnCk1scuWP53bNtcY12CtHSR440oKeO6ZK1cRBGeCKaSQMETFv5GuswMf2GsXhzHoafRNefH16bnSqwLWlEk0dIzDTuCRzvTlUyyh9Kby-43o"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
<div class="absolute bottom-6 left-6">
<h3 class="text-2xl font-bold text-white mb-1">Gạch ống</h3>
<p class="text-slate-300 text-sm">Gạch Tuynel, gạch đờ mi, gạch thẻ</p>
</div>
<div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="bg-primary text-white p-2 rounded-full material-symbols-outlined">north_east</span>
</div>
</div>
<div class="group relative overflow-hidden rounded-2xl aspect-[4/3] cursor-pointer shadow-lg">
<img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Crushed stone and gravel pile" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBXzkwrTbZSL_ow5SX1i70H6Su30b-GTPYZNl1u14eWvnnnkAn2BSI4ukmisRD-8T6hbqO59adwE6-WCQqwiEzWz29WN_8s6MZDj4yrMgvK5YyKzxmj5EEXOSD70jKgvQYEga6y72487IZYd7P_9pp3Fb8qorwIvKrRD1fD3hcTsB9RiNUy8QTkPzfziBOXlS1dy4Ud4YGLIL9mRabr5HXMx-HPTXyHNDUgHtEU4FKKWdSx7QjaETJnBolJrbJZ6vlXKWGYrQ3zkdQ"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
<div class="absolute bottom-6 left-6">
<h3 class="text-2xl font-bold text-white mb-1">Đá xây dựng</h3>
<p class="text-slate-300 text-sm">Đá 1x2, đá 4x6, đá xanh</p>
</div>
<div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="bg-primary text-white p-2 rounded-full material-symbols-outlined">north_east</span>
</div>
</div>
<div class="group relative overflow-hidden rounded-2xl aspect-[4/3] cursor-pointer shadow-lg">
<img class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" data-alt="Construction worker mixing cement additives" src="https://images.unsplash.com/photo-1580901368855-059c4792ff22?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fGV4Y2F2YXRvcnxlbnwwfHwwfHx8MA%3D%3D"/>
<div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
<div class="absolute bottom-6 left-6">
<h3 class="text-2xl font-bold text-white mb-1">Xe cuốc</h3>
<p class="text-slate-300 text-sm">Xe cuốc, xe lu, xe tải</p>
</div>
<div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
<span class="bg-primary text-white p-2 rounded-full material-symbols-outlined">north_east</span>
</div>
</div>
</div>
</div>
</section>
<!-- Why Choose Us -->
<section class="py-24 bg-white dark:bg-slate-900/50">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
<div>
<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-6">Tại sao nhà thầu tin chọn chúng tôi?</h2>
<p class="text-slate-600 dark:text-slate-400 mb-10 text-lg">Hơn 10 năm kinh nghiệm trong lĩnh vực cung ứng vật liệu, chúng tôi hiểu rõ yếu tố then chốt cho sự thành công của công trình.</p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary/10 text-primary flex items-center justify-center rounded-xl">
<span class="material-symbols-outlined">local_shipping</span>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-1">Giao hàng 2h</h4>
<p class="text-sm text-slate-500">Đội ngũ vận tải hùng hậu, cam kết giao hàng nhanh nhất nội thành.</p>
</div>
</div>
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary/10 text-primary flex items-center justify-center rounded-xl">
<span class="material-symbols-outlined">payments</span>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-1">Giá sỉ cạnh tranh</h4>
<p class="text-sm text-slate-500">Chiết khấu cực tốt cho nhà thầu và các đại lý cấp 2.</p>
</div>
</div>
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary/10 text-primary flex items-center justify-center rounded-xl">
<span class="material-symbols-outlined">verified</span>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-1">Chất lượng kiểm định</h4>
<p class="text-sm text-slate-500">100% hàng chính hãng, đầy đủ CO/CQ từ nhà máy.</p>
</div>
</div>
<div class="flex gap-4">
<div class="flex-shrink-0 w-12 h-12 bg-primary/10 text-primary flex items-center justify-center rounded-xl">
<span class="material-symbols-outlined">support_agent</span>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-1">Hỗ trợ 24/7</h4>
<p class="text-sm text-slate-500">Tư vấn kỹ thuật và xử lý đơn hàng mọi lúc bạn cần.</p>
</div>
</div>
</div>
</div>
<div class="relative">
<div class="bg-primary/5 rounded-2xl p-4 aspect-square relative overflow-hidden">
<img class="rounded-xl w-full h-full object-cover shadow-2xl relative z-10" data-alt="Construction site manager reviewing blueprints" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCEvQpqInLRtRhoCAN-Qn6a4lEi5rHuLZ1z9oIUjo_dh0RVlJrl0_r2lg6f24RxTmj8krfJHhiqL3iY5xN5B7Mp4-YQsr_37L3jStKRQ5dIR657wlB1XREBIhI5ECQcf5cyPWdrEd4wpa2_cUTH_tbZGovK-W6xLVTt3ruAVBarlnsqdsZ7lPHeqcaOheFEaWa1R8Q_FoqEkwJrrn5I8aCGtCaDJ5YHHVENwhEmM1Mf-BkHdO7KDszIS4UPWp9WZe2zJfUAToH__fs"/>
<div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary rounded-2xl z-0"></div>
<div class="absolute -top-6 -left-6 w-32 h-32 border-4 border-primary/20 rounded-2xl z-0"></div>
</div>
</div>
</div>
</div>
</section>
<!-- Price Table Preview -->
<section id="bao-gia" class="py-24 bg-background-light dark:bg-background-dark scroll-mt-24">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
    <h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-4">Báo giá tham khảo hôm nay</h2>
    <p class="text-slate-500">Cập nhật lúc: {{ date('H:i') }} - Ngày {{ date('d/m/Y') }}</p>
</div>
<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-primary/10">
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead>
<tr class="bg-primary text-white">
<th class="px-8 py-5 text-sm font-bold uppercase tracking-wider">Tên vật liệu</th>
<th class="px-8 py-5 text-sm font-bold uppercase tracking-wider">Đơn vị</th>
<th class="px-8 py-5 text-sm font-bold uppercase tracking-wider text-right">Đơn giá (VNĐ)</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-700">
@forelse($latestPrices->take(5) as $item)
<tr class="hover:bg-primary/5 transition-colors">
<td class="px-8 py-5 font-semibold text-slate-800 dark:text-slate-200">{{ $item->name }}</td>
<td class="px-8 py-5 text-slate-600 dark:text-slate-400 font-medium">{{ $item->unit }}</td>
<td class="px-8 py-5 text-right font-bold text-primary">{{ number_format($item->price) }}đ</td>
</tr>
@empty
<tr>
<td colspan="4" class="px-8 py-10 text-center text-slate-400 italic">Hiện đang cập nhật giá vật tư mới nhất...</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<div class="p-8 bg-slate-50 dark:bg-slate-800/50 flex flex-col md:flex-row items-center justify-between gap-4">
<p class="text-sm text-slate-500 italic">* Lưu ý: Giá có thể thay đổi tùy số lượng và vị trí công trình.</p>
<a href="{{ route('quotation.download') }}" class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-lg font-bold transition-all flex items-center gap-2">
    <span class="material-symbols-outlined">download</span>
    Tải Báo Giá Đầy Đủ (Excel)
</a>
</div>
</div>
</div>
</section>
<!-- Ordering Process -->
<section class="py-24 bg-white dark:bg-slate-900/50 overflow-hidden">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-4">Quy trình đặt hàng 4 bước</h2>
<div class="h-1.5 w-24 bg-primary mx-auto rounded-full"></div>
</div>
<div class="relative">
<div class="hidden lg:block absolute top-1/2 left-0 w-full h-0.5 bg-primary/20 -translate-y-1/2"></div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 relative z-10">
<!-- Step 1 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center font-black text-2xl mb-6 shadow-xl shadow-primary/30 ring-8 ring-white dark:ring-slate-900">
                            1
                        </div>
<h4 class="font-bold text-lg mb-2">Liên hệ &amp; Tư vấn</h4>
<p class="text-slate-500 text-sm">Gửi yêu cầu qua hotline hoặc form báo giá trực tuyến.</p>
</div>
<!-- Step 2 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center font-black text-2xl mb-6 shadow-xl shadow-primary/30 ring-8 ring-white dark:ring-slate-900">
                            2
                        </div>
<h4 class="font-bold text-lg mb-2">Chốt đơn &amp; Hợp đồng</h4>
<p class="text-slate-500 text-sm">Thống nhất số lượng, đơn giá và thời gian giao nhận.</p>
</div>
<!-- Step 3 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center font-black text-2xl mb-6 shadow-xl shadow-primary/30 ring-8 ring-white dark:ring-slate-900">
                            3
                        </div>
<h4 class="font-bold text-lg mb-2">Vận chuyển 2h</h4>
<p class="text-slate-500 text-sm">Hàng được bốc xếp và vận chuyển ngay đến công trình.</p>
</div>
<!-- Step 4 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center font-black text-2xl mb-6 shadow-xl shadow-primary/30 ring-8 ring-white dark:ring-slate-900">
                            4
                        </div>
<h4 class="font-bold text-lg mb-2">Nghiệm thu &amp; Thanh toán</h4>
<p class="text-slate-500 text-sm">Kiểm tra chất lượng, khối lượng và hoàn tất thanh toán.</p>
</div>
</div>
</div>
</div>
</section>
<!-- Urgent CTA Banner -->
<section class="py-12 bg-primary">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex flex-col md:flex-row items-center justify-between gap-8 text-white">
<div class="flex items-center gap-6">
<div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined text-4xl animate-pulse">call</span>
</div>
<div>
<h3 class="text-3xl font-black">Cần vật liệu gấp?</h3>
<p class="text-lg opacity-90">Gọi ngay Hotline – Giao hàng trong 2h</p>
</div>
</div>
<div class="text-center md:text-right">
<a class="text-4xl md:text-5xl font-black hover:underline transition-all underline-offset-8" href="tel:{{ $phoneTel }}">
                        {{ $phoneRaw }}
                    </a>
</div>
</div>
</div>
</section>
<!-- Testimonials -->
<section class="py-24 bg-background-light dark:bg-background-dark">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-4">Khách hàng nói gì?</h2>
<div class="h-1.5 w-24 bg-primary mx-auto rounded-full"></div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Testimonial 1 -->
    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700">
        <div class="flex text-primary mb-4">
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
        </div>
        <p class="text-slate-600 dark:text-slate-400 mb-6 italic">"Đội xe giao hàng của Sáu Phụng rất chuyên nghiệp. Tôi đặt cát đá cho công trình ở Quận 2, chỉ trong 2 tiếng là hàng đã có mặt tại bãi."</p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                N
            </div>
            <div>
                <h5 class="font-bold">Anh Nam</h5>
                <p class="text-xs text-slate-500 uppercase">Thầu xây dựng - Quận 2</p>
            </div>
        </div>
    </div>
    <!-- Testimonial 2 -->
    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700">
        <div class="flex text-primary mb-4">
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
        </div>
        <p class="text-slate-600 dark:text-slate-400 mb-6 italic">"Giá sắt thép ở đây luôn ổn định và tốt hơn mặt bằng chung. Hóa đơn chứng từ rõ ràng, rất dễ làm việc cho doanh nghiệp."</p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">
                H
            </div>
            <div>
                <h5 class="font-bold">Chị Hạnh</h5>
                <p class="text-xs text-slate-500 uppercase">Kế toán trưởng - Gia Định Cons</p>
            </div>
        </div>
    </div>
    <!-- Testimonial 3 -->
    <div class="bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-lg border border-slate-100 dark:border-slate-700">
        <div class="flex text-primary mb-4">
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
            <span class="material-symbols-outlined fill-1">star</span>
        </div>
        <p class="text-slate-600 dark:text-slate-400 mb-6 italic">"Ấn tượng nhất là khả năng cung ứng gạch số lượng lớn trong thời gian ngắn. Sáu Phụng đã giúp công trình của chúng tôi kịp tiến độ bàng giao."</p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-lg">
                T
            </div>
            <div>
                <h5 class="font-bold">Anh Thành</h5>
                <p class="text-xs text-slate-500 uppercase">Kỹ sư giám sát - VinHomes</p>
            </div>
        </div>
    </div>
</div>



</div>
</div>
</div>
</div>
</div>
</section>
<!-- Footer -->
<footer id="lien-he" class="bg-slate-900 text-slate-400 py-16 scroll-mt-24">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
<div class="col-span-1 lg:col-span-1">
<div class="flex items-center gap-3 text-white mb-6">
<div class="bg-primary p-2 rounded-lg">
<span class="material-symbols-outlined text-2xl">home_work</span>
</div>
<span class="text-xl font-black uppercase text-white">{{ $brandName }}</span>
</div>
<p class="text-sm leading-relaxed mb-6">
                        @if($storeInfo && $storeInfo->note)
                            {{ $storeInfo->note }}
                        @else
                            Tổng kho phân phối vật liệu xây dựng hàng đầu khu vực phía Nam. Cam kết uy tín, chất lượng và dịch vụ tận tâm.
                        @endif
                    </p>
<div class="flex gap-4">
<a class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition-all" href="tel:{{ $phoneTel }}" title="Gọi điện">
<span class="material-symbols-outlined text-lg">call</span>
</a>
<a class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition-all" href="{{ url('/#bao-gia') }}" title="Báo giá">
<span class="material-symbols-outlined text-lg">request_quote</span>
</a>
</div>
</div>
<div>
<h4 class="text-white font-bold mb-6">Sản phẩm chính</h4>
<ul class="space-y-4 text-sm">
<li><a class="hover:text-primary transition-colors" href="{{ url('/#danh-muc') }}">Danh mục vật liệu</a></li>
<li><a class="hover:text-primary transition-colors" href="{{ url('/#bao-gia') }}">Bảng giá tham khảo</a></li>
<li><a class="hover:text-primary transition-colors" href="{{ route('quotation.download') }}">Tải báo giá Excel</a></li>
</ul>
</div>
<div>
<h4 class="text-white font-bold mb-6">Hỗ trợ khách hàng</h4>
<ul class="space-y-4 text-sm">
<li><a class="hover:text-primary transition-colors" href="{{ url('/#lien-he') }}">Liên hệ &amp; địa chỉ</a></li>
<li><a class="hover:text-primary transition-colors" href="{{ route('login') }}">Đăng nhập quản trị</a></li>
<li><a class="hover:text-primary transition-colors" href="{{ url('/#bao-gia') }}">Yêu cầu báo giá</a></li>
</ul>
</div>
<div>
<h4 class="text-white font-bold mb-6">Thông tin liên hệ</h4>
<ul class="space-y-4 text-sm">
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary shrink-0">location_on</span>
<span>{{ $storeInfo?->address ?? 'số 222 Đường Số 6, Bình Trưng Đông, Thủ Đức, TP. Hồ Chí Minh, Việt Nam' }}</span>
</li>
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary shrink-0">phone</span>
<span><a href="tel:{{ $phoneTel }}" class="hover:text-primary">{{ $phoneRaw }}</a></span>
</li>
@if($storeInfo && ($storeInfo->bank_name || $storeInfo->bank_account))
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary shrink-0">account_balance</span>
<span>
    @if($storeInfo->bank_name){{ $storeInfo->bank_name }}@endif
    @if($storeInfo->bank_account) — STK: {{ $storeInfo->bank_account }}@endif
    @if($storeInfo->bank_owner) ({{ $storeInfo->bank_owner }})@endif
</span>
</li>
@endif
</ul>
</div>
</div>
<div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
<p>© {{ date('Y') }} {{ $brandName }}. All rights reserved.</p>
<div class="flex gap-6">
<a class="hover:text-white" href="{{ url('/#lien-he') }}">Liên hệ</a>
<a class="hover:text-white" href="{{ route('landing') }}">Trang chủ</a>
</div>
</div>
</div>
</footer>
</body></html>