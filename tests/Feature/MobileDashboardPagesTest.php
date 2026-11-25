<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;

beforeEach(function () {
    useMobileUserAgent();
    actingAsAdmin();

    $this->customer = Customer::query()->create(['name' => 'Thầu Khung', 'phone' => '0977777777']);
    $this->project = Project::query()->create([
        'customer_id' => $this->customer->id,
        'name' => 'Công trình Khung',
        'address' => 'Quận 9',
        'end_date' => '2026-12-31',
        'total_invoice' => 1500000,
        'total_paid' => 500000,
        'total_debt' => 1000000,
    ]);
});

function makeInvoice(Project $project): Invoice
{
    return Invoice::query()->create([
        'project_id' => $project->id,
        'code' => 'HD-T1',
        'invoice_date' => now(),
        'total_amount' => 1500000,
    ]);
}

it('renders the mobile invoice list inside the dashboard shell', function () {
    $invoice = makeInvoice($this->project);

    $html = $this->get(route('projects.invoices.index', $this->project))->assertOk()->getContent();

    $card = (string) str($html)->after('data-invoice-card="'.$invoice->id.'"')->before('</article>');

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain(route('customers.projects.index', $this->project->customer_id))
        ->toContain(route('projects.invoices.create', $this->project))
        ->toContain('@submit="isLoading = true"')
        ->not->toContain('handleTouchMove');

    expect($card)
        ->toContain('HD-T1')
        ->toContain(route('invoices.show', $invoice))
        ->toContain(route('invoices.pdf', $invoice))
        ->toContain(route('invoices.edit', $invoice))
        ->toContain(route('invoices.destroy', $invoice));
});

it('renders the mobile invoice detail with print-safe chrome', function () {
    $invoice = makeInvoice($this->project);

    $html = $this->get(route('invoices.show', $invoice))->assertOk()->getContent();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('HD-T1')
        ->toContain('window.print()')
        ->toContain(route('invoices.pdf', $invoice))
        ->toContain(route('projects.invoices.index', $this->project))
        ->toMatch('/<aside[^>]*\bprint:hidden\b/')
        ->toMatch('/<header[^>]*\bprint:hidden\b/')
        ->toMatch('/<footer[^>]*\bprint:hidden\b/');
});

it('renders the mobile invoice create form inside the dashboard shell', function () {
    $html = $this->get(route('projects.invoices.create', $this->project))->assertOk()->getContent();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('action="'.route('projects.invoices.store', $this->project).'"')
        ->toContain('@submit="isLoading = true"')
        ->toContain('x-text="`${items.length} mục`"')
        ->toContain(route('projects.invoices.index', $this->project))
        ->not->toContain('General Information')
        ->not->toContain('Invoice Items')
        ->not->toContain('Add Item')
        ->not->toContain('Total Amount');
});

it('renders the mobile invoice edit form with a usable date', function () {
    $invoice = makeInvoice($this->project);

    $html = $this->get(route('invoices.edit', $invoice))->assertOk()->getContent();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('action="'.route('invoices.update', $invoice).'"')
        ->toContain('name="_method" value="PUT"')
        ->toContain('name="invoice_date" value="'.now()->format('Y-m-d').'"')
        ->toContain('x-text="`${items.length} mục`"')
        ->toContain(route('invoices.destroy', $invoice))
        ->not->toContain('Invoice Inc');
});

it('renders the mobile Excel upload step inside the dashboard shell', function () {
    $html = $this->get(route('invoice.index'))->assertOk()->getContent();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('action="'.route('invoice.import').'"')
        ->toContain('@submit="isLoading = true"')
        ->toContain('Thầu Khung')
        ->not->toContain('dark_mode');
});

it('renders the mobile sheet selection step inside the dashboard shell', function () {
    $html = view('import-sheet-selection-mobile', [
        'sheetNames' => ['Sheet1', 'Sheet2'],
        'storedFilePath' => 'imports/x.xlsx',
        'projectId' => null,
    ])->render();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('Sheet2')
        ->toContain('Tiếp tục')
        ->toContain('action="'.route('invoice.import').'"')
        ->toContain('@submit="isLoading = true"');
});

it('renders the mobile import preview step inside the dashboard shell', function () {
    $html = view('import-result-mobile', [
        'khachHang' => 'Thầu A',
        'diaChi' => '',
        'dienThoai' => '',
        'items' => [[
            'stt' => 1, 'ngay_thang' => '01/11/2026', 'ten_vat_lieu' => 'Xi măng', 'don_vi_tinh' => 'bao',
            'so_luong' => 10, 'don_gia' => 90000, 'thanh_tien' => 900000,
        ]],
        'advancePayments' => [],
        'tongTien' => 900000,
        'tongTienUng' => 0,
        'tongTienChu' => 'Chín trăm nghìn đồng',
        'ngayCuoi' => '01/11/2026',
        'projectId' => null,
    ])->render();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('Xi măng')
        ->toContain('action="'.route('invoice.save').'"')
        ->toContain('@submit="isLoading = true"')
        ->not->toContain('dark_mode');
});

it('renders customer projects inside the dashboard shell', function () {
    $html = $this->get(route('customers.projects.index', $this->customer))->assertOk()->getContent();

    $card = (string) str($html)->after('data-project-card="'.$this->project->id.'"')->before('</article>');

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain(route('customers.index'))
        ->toContain('@submit="isLoading = true"')
        ->not->toContain('handleTouchMove')
        ->not->toContain('Dec 31, 2026');

    expect($card)
        ->toContain('Công trình Khung')
        ->toContain('31/12/2026')
        ->toContain(route('projects.invoices.index', $this->project))
        ->toContain('openEdit(')
        ->toContain("confirmDelete('".route('customers.projects.destroy', ['customer' => $this->customer->id, 'project' => $this->project->id])."')");
});
