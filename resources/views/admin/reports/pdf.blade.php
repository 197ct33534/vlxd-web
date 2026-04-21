<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color: #111; }
        h1 { font-size: 14pt; margin: 0 0 8px 0; }
        .meta { font-size: 9pt; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-size: 9pt; }
        .num { text-align: right; }
        .section { margin-top: 18px; font-weight: bold; font-size: 11pt; }
        .totals { margin: 12px 0; font-size: 10pt; }
        .totals span { margin-right: 24px; }
    </style>
</head>
<body>
    <h1>{{ __('reports.pdf_title') }}</h1>
    <div class="meta">
        @if($storeInfo?->name)
            {{ $storeInfo->name }}<br>
        @endif
        {{ __('reports.pdf_generated') }}: {{ $generatedAt->format('d/m/Y H:i') }}
    </div>

    <div class="totals">
        <span><strong>{{ __('reports.kpi_total_invoiced') }}:</strong> {{ number_format($totals['invoice'], 0, ',', '.') }} đ</span>
        <span><strong>{{ __('reports.kpi_total_paid') }}:</strong> {{ number_format($totals['payments'], 0, ',', '.') }} đ</span>
        <span><strong>{{ __('reports.kpi_total_debt') }}:</strong> {{ number_format($totals['debt'], 0, ',', '.') }} đ</span>
    </div>

    <div class="section">{{ __('reports.table_debt_title') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('reports.col_customer') }}</th>
                <th>{{ __('reports.col_project') }}</th>
                <th class="num">{{ __('reports.col_invoiced') }}</th>
                <th class="num">{{ __('reports.col_paid') }}</th>
                <th class="num">{{ __('reports.col_debt') }}</th>
                <th>{{ __('reports.export.debt_col_address') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($projects as $p)
                <tr>
                    <td>{{ $p->customer->name ?? '—' }}</td>
                    <td>{{ $p->name }}</td>
                    <td class="num">{{ number_format($p->total_invoice, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($p->total_paid, 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($p->total_debt, 0, ',', '.') }}</td>
                    <td>{{ $p->address ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section">{{ __('reports.pdf_monthly_section') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('reports.export.revenue_col_month') }}</th>
                <th class="num">{{ __('reports.export.revenue_col_invoiced') }}</th>
                <th class="num">{{ __('reports.export.revenue_col_paid') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($chartLabels as $i => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="num">{{ number_format($invoiceTotals[$i], 0, ',', '.') }}</td>
                    <td class="num">{{ number_format($paymentTotals[$i], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
