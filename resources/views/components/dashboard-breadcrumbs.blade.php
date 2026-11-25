@props(['importProject' => null])

@php
    $name = request()->route()?->getName();
    $invoice = request()->route('invoice');
    $project = request()->route('project') ?? $invoice?->project;
    $customer = request()->route('customer');
    $crumbs = [];
    $sections = [
        'customers.index' => 'Khách hàng',
        'projects.index' => 'Tất cả dự án',
        'material-prices.index' => 'Báo giá vật tư',
        'daily-reports.index' => 'Báo cáo ngày',
        'admin.orders.index' => 'Đơn mobile',
        'admin.inventory.index' => 'Tồn kho bãi',
        'admin.stock-receipts.index' => 'Nhập kho',
        'admin.customers.debt' => 'Sổ nợ khách',
        'admin.reports.index' => __('nav.reports'),
        'employees.index' => 'Nhân viên',
        'store-settings.edit' => 'Thông tin cửa hàng',
        'driver.index' => 'Tài xế',
    ];
    $pages = [
        'material-prices.create' => ['material-prices.index', 'Thêm vật tư'],
        'material-prices.edit' => ['material-prices.index', 'Sửa giá vật tư'],
        'daily-reports.create' => ['daily-reports.index', 'Tạo báo cáo'],
        'daily-reports.show' => ['daily-reports.index', 'Chi tiết báo cáo'],
        'daily-reports.edit' => ['daily-reports.index', 'Sửa báo cáo'],
        'admin.orders.show' => ['admin.orders.index', 'Chi tiết đơn'],
        'admin.stock-receipts.create' => ['admin.stock-receipts.index', 'Tạo phiếu nhập'],
        'employees.create' => ['employees.index', 'Thêm nhân viên'],
        'employees.show' => ['employees.index', 'Chi tiết nhân viên'],
        'employees.edit' => ['employees.index', 'Sửa nhân viên'],
        'driver.show' => ['driver.index', 'Chi tiết giao hàng'],
    ];

    if (isset($sections[$name])) {
        $crumbs[] = [$sections[$name], null];
    } elseif (isset($pages[$name])) {
        [$parent, $label] = $pages[$name];
        $crumbs = [[$sections[$parent], route($parent)], [$label, null]];
    } elseif ($name === 'customers.projects.index' && $customer) {
        $crumbs = [['Khách hàng', route('customers.index')], [$customer->name, null]];
    } elseif (in_array($name, ['projects.invoices.index', 'projects.invoices.create', 'invoices.show', 'invoices.edit', 'projects.price_history', 'invoice.index', 'invoice.workbook'], true)) {
        $context = in_array($name, ['invoice.index', 'invoice.workbook'], true) ? $importProject : $project;
        if ($context) {
            $crumbs = [
                ['Khách hàng', route('customers.index')],
                [$context->customer->name, route('customers.projects.index', $context->customer_id)],
                [$context->name, $name === 'projects.invoices.index' ? null : route('projects.invoices.index', $context)],
            ];
        }
        if ($name === 'invoices.edit') {
            $crumbs[] = [$invoice->code ?: 'Hóa đơn #'.$invoice->id, route('invoices.show', $invoice)];
        }
        if ($name === 'invoice.workbook') {
            $crumbs[] = ['Nhập Excel', route('invoice.index', $context ? ['project_id' => $context->id] : [])];
        }
        $label = match ($name) {
            'projects.invoices.create' => 'Tạo hóa đơn',
            'invoices.show' => $invoice->code ?: 'Hóa đơn #'.$invoice->id,
            'invoices.edit' => 'Sửa hóa đơn',
            'projects.price_history' => 'Lịch sử giá',
            'invoice.index' => 'Nhập Excel',
            'invoice.workbook' => 'Xem trước',
            default => null,
        };
        if ($label !== null) {
            $crumbs[] = [$label, null];
        }
    }
@endphp

@if ($crumbs)
    <nav aria-label="Đường dẫn điều hướng"
        class="shrink-0 border-b border-border-light bg-container-light px-3 dark:border-border-dark dark:bg-container-dark sm:px-5 lg:px-8 print:hidden">
        <ol class="flex flex-wrap items-center gap-x-2 text-sm text-gray-600 dark:text-gray-400">
            @foreach ([['Tổng quan', route('dashboard')], ...$crumbs] as [$label, $url])
                <li class="flex min-w-0 max-w-full items-center gap-2">
                    @unless ($loop->first)
                        <span aria-hidden="true" class="shrink-0 text-gray-400">/</span>
                    @endunless
                    @if ($url)
                        <a href="{{ $url }}" class="inline-flex min-h-11 items-center break-words rounded font-medium hover:text-primary hover:underline">{{ $label }}</a>
                    @else
                        <span aria-current="page" class="py-2.5 break-words font-semibold text-text-light dark:text-text-dark">{{ $label }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
