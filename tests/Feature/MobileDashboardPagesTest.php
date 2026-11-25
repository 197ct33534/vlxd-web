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
