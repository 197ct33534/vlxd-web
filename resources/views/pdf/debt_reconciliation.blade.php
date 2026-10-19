<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
        .right { text-align: right; }
        .sign { margin-top: 36px; width: 100%; }
        .sign td { border: 0; width: 50%; text-align: center; vertical-align: top; height: 90px; }
    </style>
</head>
<body>
    <h1>{{ $store->name ?? 'Cửa hàng vật liệu xây dựng' }}</h1>
    <p class="muted">{{ $store->address ?? '' }} {{ $store->phone ? ' · '.$store->phone : '' }}</p>
    <h2>Biên bản đối soát công nợ</h2>
    <p>Khách hàng: <strong>{{ $project->customer?->name }}</strong></p>
    <p>Công trình: <strong>{{ $project->name }}</strong></p>
    <p>Kỳ: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</p>

    <p>Số dư đầu kỳ: <strong>{{ number_format($opening, 0, ',', '.') }} đ</strong></p>

    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Chứng từ</th>
                <th>Diễn giải</th>
                <th class="right">Phát sinh nợ</th>
                <th class="right">Đã thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_date?->format('d/m/Y') }}</td>
                    <td>{{ $invoice->code }}</td>
                    <td>{{ $invoice->note }}</td>
                    <td class="right">{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    <td class="right"></td>
                </tr>
            @endforeach
            @foreach($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                    <td>PT {{ $payment->id }}</td>
                    <td>{{ $payment->note }} {{ $payment->reference_code }}</td>
                    <td class="right"></td>
                    <td class="right">{{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Cộng trong kỳ</td>
                <td class="right">{{ number_format($periodInvoice, 0, ',', '.') }}</td>
                <td class="right">{{ number_format($periodPaid, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p>Số dư cuối kỳ: <strong>{{ number_format($closing, 0, ',', '.') }} đ</strong></p>
    <p>Công nợ trên sổ công trình: <strong>{{ number_format($project->total_debt, 0, ',', '.') }} đ</strong></p>
    @if($coversBook)
        <p class="muted">Kỳ đối soát phủ toàn bộ chứng từ đến ngày chốt, số dư cuối kỳ khớp sổ công nợ.</p>
    @endif

    <table class="sign">
        <tr>
            <td>
                Đại diện cửa hàng<br><br><br>
                (Ký, ghi rõ họ tên)
            </td>
            <td>
                Đại diện nhà thầu / khách hàng<br><br><br>
                (Ký, ghi rõ họ tên)
            </td>
        </tr>
    </table>
</body>
</html>
