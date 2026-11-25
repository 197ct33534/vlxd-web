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
