@extends('layouts.dashboard')

@section('title', 'Tổng quan')

@section('page_heading', 'Tổng quan')

@section('content')
    <div class="flex flex-col gap-8">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Tổng quan</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Doanh thu theo hóa đơn, thu tiền và công nợ hiện tại (12 tháng gần nhất).</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Tổng giá trị HĐ (lũy kế)</p>
                <p class="mt-2 text-2xl font-black text-text-light dark:text-text-dark">{{ number_format($totalInvoiceAmount, 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500">đ</span></p>
            </div>
            <div class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Đã thu (lũy kế)</p>
                <p class="mt-2 text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($totalPayments, 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500">đ</span></p>
            </div>
            <div class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Công nợ dự án (hiện tại)</p>
                <p class="mt-2 text-2xl font-black text-red-600 dark:text-red-400">{{ number_format($totalDebt, 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500">đ</span></p>
            </div>
        </div>

        <div class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-6 shadow-subtle">
            <h2 class="text-lg font-bold text-text-light dark:text-text-dark mb-4">12 tháng: Giá trị hóa đơn vs Thu tiền</h2>
            <div class="h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">Giá trị hóa đơn: theo <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">invoice_date</code>. Thu tiền: theo <code class="text-xs bg-gray-100 dark:bg-gray-800 px-1 rounded">payment_date</code>.</p>
        </div>

        <div>
            <h2 class="text-lg font-bold text-text-light dark:text-text-dark mb-4">Truy cập nhanh</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('customers.index') }}" class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle hover:border-primary/40 hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">group</span>
                    <h3 class="mt-3 font-bold text-text-light dark:text-text-dark">Khách hàng</h3>
                    <p class="mt-1 text-sm text-gray-500">Danh sách, dự án theo khách</p>
                </a>
                <a href="{{ route('projects.index') }}" class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle hover:border-primary/40 hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">folder</span>
                    <h3 class="mt-3 font-bold text-text-light dark:text-text-dark">Tất cả dự án</h3>
                    <p class="mt-1 text-sm text-gray-500">Hóa đơn & nhập Excel</p>
                </a>
                <a href="{{ route('material-prices.index') }}" class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle hover:border-primary/40 hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">sell</span>
                    <h3 class="mt-3 font-bold text-text-light dark:text-text-dark">Báo giá vật tư</h3>
                    <p class="mt-1 text-sm text-gray-500">Landing & Excel</p>
                </a>
                <a href="{{ route('invoice.index') }}" class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle hover:border-primary/40 hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">upload_file</span>
                    <h3 class="mt-3 font-bold text-text-light dark:text-text-dark">Nhập hóa đơn</h3>
                    <p class="mt-1 text-sm text-gray-500">Import Excel</p>
                </a>
                <a href="{{ route('store-settings.edit') }}" class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle hover:border-primary/40 hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">storefront</span>
                    <h3 class="mt-3 font-bold text-text-light dark:text-text-dark">Thông tin cửa hàng</h3>
                    <p class="mt-1 text-sm text-gray-500">PDF / in</p>
                </a>
                <a href="{{ route('daily-reports.index') }}" class="rounded-xl border border-border-light dark:border-border-dark bg-container-light dark:bg-container-dark p-5 shadow-subtle hover:border-primary/40 hover:bg-primary/5 transition-colors">
                    <span class="material-symbols-outlined text-primary text-3xl">assignment</span>
                    <h3 class="mt-3 font-bold text-text-light dark:text-text-dark">Báo cáo ngày</h3>
                    <p class="mt-1 text-sm text-gray-500">Theo dõi & duyệt</p>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($chartLabels);
        const invoiceData = @json($invoiceTotals);
        const paymentData = @json($paymentTotals);

        const fmt = (n) => new Intl.NumberFormat('vi-VN').format(Math.round(n));

        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Giá trị HĐ (theo tháng lập HĐ)',
                        data: invoiceData,
                        backgroundColor: 'rgba(0, 123, 255, 0.55)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1,
                    },
                    {
                        label: 'Thu tiền (theo tháng thanh toán)',
                        data: paymentData,
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (v) => fmt(v),
                        },
                    },
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ctx.dataset.label + ': ' + fmt(ctx.raw) + ' đ',
                        },
                    },
                },
            },
        });
    </script>
@endsection
