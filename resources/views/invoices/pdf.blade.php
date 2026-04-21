<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hóa đơn {{ $invoice->code ?? $invoice->id }}</title>
    <style>
        @page { margin: 14mm 16mm; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5pt;
            line-height: 1.45;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        .doc-wrap { width: 100%; }

        /* Thanh nhận diện */
        .brand-bar {
            height: 4px;
            background: #1d4ed8;
            margin: 0 0 14px 0;
            border-radius: 2px;
        }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: top; padding: 0; }
        .seller { padding-right: 12px; }
        .seller-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.02em;
        }
        .seller-line {
            font-size: 9pt;
            color: #475569;
            margin: 2px 0;
        }
        .doc-badge-wrap { text-align: right; vertical-align: top; }
        table.doc-badge {
            margin-left: auto;
            border: 2px solid #1d4ed8;
            border-radius: 6px;
            background: #f8fafc;
            border-collapse: separate;
        }
        table.doc-badge td { padding: 10px 14px; text-align: right; }
        .doc-title {
            font-size: 15pt;
            font-weight: bold;
            color: #1e40af;
            letter-spacing: 0.08em;
            margin: 0 0 6px 0;
        }
        .doc-meta { font-size: 9.5pt; color: #334155; }
        .doc-meta strong { color: #0f172a; }

        /* Khối thông tin khách / công trình */
        .section {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .section-head {
            background: #f1f5f9;
            padding: 6px 12px;
            font-size: 9pt;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid #e2e8f0;
        }
        .section-body { padding: 0; }
        table.info-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table.info-table td { padding: 6px 12px; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        table.info-table tr:last-child td { border-bottom: none; }
        table.info-table .label { color: #64748b; width: 100px; white-space: nowrap; }
        table.info-table .value { color: #0f172a; font-weight: bold; }
        table.info-table .value-normal { font-weight: normal; color: #334155; }

        /* Bảng hàng hóa */
        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        table.items thead th {
            background: #1e40af;
            color: #fff;
            font-weight: bold;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #1e3a8a;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        table.items thead th:nth-child(1) { width: 6%; text-align: center; }
        table.items thead th:nth-child(2) { width: 38%; }
        table.items thead th:nth-child(3) { width: 8%; text-align: center; }
        table.items thead th:nth-child(4) { width: 10%; text-align: center; }
        table.items thead th:nth-child(5) { width: 16%; text-align: right; }
        table.items thead th:nth-child(6) { width: 18%; text-align: right; }

        table.items tbody td {
            border: 1px solid #e2e8f0;
            padding: 7px 6px;
            vertical-align: top;
        }
        table.items tbody tr:nth-child(even) { background: #f8fafc; }
        .col-num { text-align: center; color: #64748b; }
        .col-right { text-align: right; }

        /* Tổng cộng */
        .totals-wrap { margin-top: 0; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 6px 6px; overflow: hidden; }
        table.totals { width: 100%; border-collapse: collapse; }
        table.totals td { padding: 10px 12px; font-size: 11pt; }
        table.totals .label-total {
            text-align: right;
            color: #475569;
            font-weight: bold;
            background: #f1f5f9;
            width: 75%;
            border-top: 2px solid #cbd5e1;
        }
        table.totals .amount-total {
            text-align: right;
            font-weight: bold;
            font-size: 13pt;
            color: #1e40af;
            background: #eff6ff;
            border-top: 2px solid #93c5fd;
            white-space: nowrap;
        }
        .total-text {
            padding: 10px 12px;
            font-size: 9.5pt;
            color: #334155;
            line-height: 1.5;
            text-align: left;
            border: 1px solid #e2e8f0;
            border-top: none;
            background: #fafafa;
        }
        .total-text .amount-words { font-weight: bold; color: #0f172a; font-style: normal; }
        .total-text .legacy-note { margin-top: 6px; font-size: 8.5pt; color: #94a3b8; font-style: italic; }

        /* Chữ ký */
        table.signatures { width: 100%; border-collapse: collapse; margin-top: 22px; }
        table.signatures td { width: 50%; vertical-align: top; padding: 8px 16px; text-align: center; }
        .sign-role { font-size: 10pt; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .sign-hint { font-size: 8.5pt; color: #64748b; margin-bottom: 10px; }
        .sign-space { height: 56px; border-bottom: 1px solid #cbd5e1; margin: 0 12px 8px 12px; }
        .sign-name-line { font-size: 8.5pt; color: #94a3b8; }

        .footer-note {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
            font-size: 8.5pt;
            color: #94a3b8;
            text-align: center;
            line-height: 1.5;
        }
        .store-footnote { font-size: 8.5pt; color: #64748b; margin-top: 12px; padding: 8px 10px; background: #f8fafc; border-radius: 4px; border-left: 3px solid #94a3b8; }
    </style>
</head>
<body>
<div class="doc-wrap">
    <div class="brand-bar"></div>

    <table class="header-table">
        <tr>
            <td class="seller" style="width: 55%;">
                @if($storeInfo)
                    <div class="seller-name">{{ $storeInfo->name }}</div>
                    @if($storeInfo->address)
                        <div class="seller-line">{{ $storeInfo->address }}</div>
                    @endif
                    @if($storeInfo->phone)
                        <div class="seller-line">Điện thoại: {{ $storeInfo->phone }}</div>
                    @endif
                    @if($storeInfo->bank_name || $storeInfo->bank_account)
                        <div class="seller-line">
                            @if($storeInfo->bank_name){{ $storeInfo->bank_name }}@endif
                            @if($storeInfo->bank_account)
                                @if($storeInfo->bank_name) — @endif
                                STK: {{ $storeInfo->bank_account }}
                            @endif
                            @if($storeInfo->bank_owner) — {{ $storeInfo->bank_owner }}@endif
                        </div>
                    @endif
                @else
                    <div class="seller-name">Đơn vị bán hàng</div>
                    <div class="seller-line">Vui lòng cập nhật thông tin cửa hàng trong hệ thống.</div>
                @endif
            </td>
            <td class="doc-badge-wrap" style="width: 45%;" align="right">
                <table class="doc-badge" cellpadding="0" cellspacing="0" align="right">
                    <tr>
                        <td>
                            <div class="doc-title">HÓA ĐƠN BÁN HÀNG</div>
                            <div class="doc-meta">
                                <div style="margin-bottom: 3px;">Số: <strong>{{ $invoice->code ?? 'HD-'.str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
                                <div>Ngày lập: <strong>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') : '—' }}</strong></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="section-head">Thông tin khách hàng &amp; công trình</div>
        <div class="section-body">
            <table class="info-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="label">Khách hàng</td>
                    <td class="value">{{ $invoice->project->customer->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Công trình</td>
                    <td class="value">{{ $invoice->project->name ?? '—' }}</td>
                </tr>
                @if($invoice->project->address)
                    <tr>
                        <td class="label">Địa chỉ</td>
                        <td class="value-normal">{{ $invoice->project->address }}</td>
                    </tr>
                @endif
                @if($invoice->note)
                    <tr>
                        <td class="label">Ghi chú</td>
                        <td class="value-normal">{{ $invoice->note }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên hàng hóa, dịch vụ</th>
                <th>ĐVT</th>
                <th>SL</th>
                <th>Đơn giá (đ)</th>
                <th>Thành tiền (đ)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="col-num">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="col-num">{{ $item->unit }}</td>
                    <td class="col-num">{{ $item->quantity }}</td>
                    <td class="col-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="col-right">{{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals">
            <tr>
                <td class="label-total">Tổng thanh toán</td>
                <td class="amount-total">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</td>
            </tr>
        </table>
        <div class="total-text">
            <span>Bằng chữ: </span><span class="amount-words">{{ $amountInWords }}</span>
            @if($invoice->total_text && trim((string) $invoice->total_text) !== '')
                <div class="legacy-note">Theo hóa đơn gốc / nhập liệu: {{ $invoice->total_text }}</div>
            @endif
        </div>
    </div>

    <table class="signatures" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="sign-role">Khách hàng</div>
                <div class="sign-hint">(Ký, ghi rõ họ tên)</div>
                <div class="sign-space"></div>
                <div class="sign-name-line">{{ $invoice->project->customer->name ?? '…………………………' }}</div>
            </td>
            <td>
                <div class="sign-role">Người bán hàng</div>
                <div class="sign-hint">(Ký, ghi rõ họ tên, đóng dấu)</div>
                <div class="sign-space"></div>
                <div class="sign-name-line">{{ optional($storeInfo)->name ?: '…………………………' }}</div>
            </td>
        </tr>
    </table>

    @if($storeInfo && $storeInfo->note)
        <div class="store-footnote">{{ $storeInfo->note }}</div>
    @endif

    <div class="footer-note">
        Chứng từ được tạo từ hệ thống quản lý &middot; In lúc {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>
