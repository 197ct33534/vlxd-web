@extends('layouts.dashboard')

@section('title', __('reports.title'))

@section('page_heading', __('reports.title'))

@section('content')
    <div class="flex flex-col gap-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-text-light dark:text-text-dark">{{ __('reports.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('reports.subtitle') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reports.export.debt_excel') }}" data-no-loading class="inline-flex items-center gap-2 rounded-lg border border-emerald-600 bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-800 transition hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-200 dark:hover:bg-emerald-900/50">
                    <span class="material-symbols-outlined text-lg">table</span>
                    {{ __('reports.export.debt_excel') }}
                </a>
                <a href="{{ route('admin.reports.export.revenue_excel') }}" data-no-loading class="inline-flex items-center gap-2 rounded-lg border border-blue-600 bg-blue-50 px-4 py-2.5 text-sm font-bold text-blue-800 transition hover:bg-blue-100 dark:border-blue-700 dark:bg-blue-950/40 dark:text-blue-200 dark:hover:bg-blue-900/50">
                    <span class="material-symbols-outlined text-lg">bar_chart</span>
                    {{ __('reports.export.revenue_excel') }}
                </a>
                <a href="{{ route('admin.reports.export.summary_pdf') }}" data-no-loading target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-violet-600 bg-violet-50 px-4 py-2.5 text-sm font-bold text-violet-800 transition hover:bg-violet-100 dark:border-violet-700 dark:bg-violet-950/40 dark:text-violet-200 dark:hover:bg-violet-900/50">
                    <span class="material-symbols-outlined text-lg">picture_as_pdf</span>
                    {{ __('reports.export.summary_pdf') }}
                </a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-border-light bg-container-light p-5 shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('reports.kpi_total_invoiced') }}</p>
                <p class="mt-2 text-2xl font-black text-text-light dark:text-text-dark">{{ number_format($totals['invoice'], 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500">đ</span></p>
            </div>
            <div class="rounded-xl border border-border-light bg-container-light p-5 shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('reports.kpi_total_paid') }}</p>
                <p class="mt-2 text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($totals['payments'], 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500">đ</span></p>
            </div>
            <div class="rounded-xl border border-border-light bg-container-light p-5 shadow-subtle dark:border-border-dark dark:bg-container-dark">
                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('reports.kpi_total_debt') }}</p>
                <p class="mt-2 text-2xl font-black text-red-600 dark:text-red-400">{{ number_format($totals['debt'], 0, ',', '.') }} <span class="text-sm font-semibold text-gray-500">đ</span></p>
            </div>
        </div>

        <div class="rounded-xl border border-border-light bg-container-light p-6 shadow-subtle dark:border-border-dark dark:bg-container-dark">
            <h2 class="mb-4 text-lg font-bold text-text-light dark:text-text-dark">{{ __('reports.chart_12m') }}</h2>
            <div class="h-72 w-full">
                <canvas id="adminReportChart"></canvas>
            </div>
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __('reports.chart_hint') }}</p>
        </div>

        <div class="overflow-hidden rounded-xl border border-border-light bg-container-light shadow-subtle dark:border-border-dark dark:bg-container-dark">
            <div class="border-b border-border-light px-4 py-3 dark:border-border-dark">
                <h2 class="text-lg font-bold text-text-light dark:text-text-dark">{{ __('reports.table_debt_title') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-left text-sm">
                    <thead class="border-b border-border-light bg-background-light text-xs font-bold uppercase text-gray-600 dark:border-border-dark dark:bg-background-dark dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('reports.col_customer') }}</th>
                            <th class="px-4 py-3">{{ __('reports.col_project') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('reports.col_invoiced') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('reports.col_paid') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('reports.col_debt') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light dark:divide-border-dark">
                        @forelse($projects as $p)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $p->customer->name ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $p->name }}</td>
                                <td class="px-4 py-3 text-right tabular-nums">{{ number_format($p->total_invoice, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right tabular-nums text-green-600 dark:text-green-400">{{ number_format($p->total_paid, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-semibold tabular-nums text-red-600 dark:text-red-400">{{ number_format($p->total_debt, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('reports.empty_projects') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($chartLabels);
        const invoiceData = @json($invoiceTotals);
        const paymentData = @json($paymentTotals);
        const fmt = (n) => new Intl.NumberFormat('vi-VN').format(Math.round(n));
        new Chart(document.getElementById('adminReportChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: @json(__('reports.chart_ds_invoiced')),
                        data: invoiceData,
                        backgroundColor: 'rgba(59, 130, 246, 0.55)',
                        borderRadius: 4,
                    },
                    {
                        label: @json(__('reports.chart_ds_paid')),
                        data: paymentData,
                        backgroundColor: 'rgba(16, 185, 129, 0.55)',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: (v) => fmt(v) } },
                    x: { stacked: false },
                },
                plugins: { legend: { position: 'bottom' } },
            },
        });
    </script>
@endsection
