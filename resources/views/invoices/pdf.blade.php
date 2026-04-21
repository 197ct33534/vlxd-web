<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hóa đơn {{ $invoice->code ?? $invoice->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .muted { color: #555; font-size: 9px; }
        h1 { font-size: 18px; margin: 0 0 4px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 6px 8px; }
        table.items th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; }
        .right { text-align: right; }
        .center { text-align: center; }
        .total-row td { font-weight: bold; font-size: 12px; border-top: 2px solid #333; }
        .header-block { margin-bottom: 16px; }
        .store-name { font-size: 14px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header-block">
        @if($storeInfo)
            <div class="store-name">{{ $storeInfo->name }}</div>
            @if($storeInfo->address)<div class="muted">{{ $storeInfo->address }}</div>@endif
            @if($storeInfo->phone)<div class="muted">ĐT: {{ $storeInfo->phone }}</div>@endif
            @if($storeInfo->bank_name || $storeInfo->bank_account)
                <div class="muted">
                    @if($storeInfo->bank_name){{ $storeInfo->bank_name }}@endif
                    @if($storeInfo->bank_account) — STK: {{ $storeInfo->bank_account }}@endif
                    @if($storeInfo->bank_owner) — {{ $storeInfo->bank_owner }}@endif
                </div>
            @endif
        @else
            <div class="store-name">HÓA ĐƠN BÁN HÀNG</div>
        @endif
    </div>

    <table style="width:100%; margin-bottom: 12px;">
        <tr>
            <td style="vertical-align: top;">
                <h1>HÓA ĐƠN</h1>
                <div class="muted">Số: {{ $invoice->code ?? str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</div>
            </td>
            <td class="right" style="vertical-align: top;">
                <div>Ngày: {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : '—' }}</div>
            </td>
        </tr>
    </table>

    <p><strong>Khách hàng:</strong> {{ $invoice->project->customer->name ?? '—' }}</p>
    <p><strong>Công trình:</strong> {{ $invoice->project->name ?? '—' }}</p>
    @if($invoice->project->address)
        <p class="muted">{{ $invoice->project->address }}</p>
    @endif
    @if($invoice->note)
        <p><strong>Ghi chú:</strong> {{ $invoice->note }}</p>
    @endif

    <table class="items">
        <thead>
            <tr>
                <th>Tên hàng</th>
                <th class="center">ĐVT</th>
                <th class="center">SL</th>
                <th class="right">Đơn giá</th>
                <th class="right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="center">{{ $item->unit }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="right">Tổng cộng</td>
                <td class="right">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</td>
            </tr>
            @if($invoice->total_text)
                <tr>
                    <td colspan="5" class="right muted">({{ $invoice->total_text }})</td>
                </tr>
            @endif
        </tfoot>
    </table>

    @if($storeInfo && $storeInfo->note)
        <p class="muted" style="margin-top: 16px;">{{ $storeInfo->note }}</p>
    @endif

    <p class="muted" style="margin-top: 24px; text-align: center;">Chứng từ được tạo từ hệ thống — in/ngày {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
