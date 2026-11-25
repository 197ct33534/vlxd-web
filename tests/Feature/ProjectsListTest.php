<?php

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;

it('renders a mobile card list for all projects', function () {
    $customer = Customer::query()->create(['name' => 'Thầu Test', 'phone' => '0911111111']);
    $project = Project::query()->create([
        'customer_id' => $customer->id,
        'name' => 'Công trình Test',
        'total_invoice' => 18640000,
        'total_paid' => 640000,
        'total_debt' => 18000000,
    ]);

    $html = $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('projects.index'))
        ->assertOk()
        ->getContent();

    // Danh sách thẻ chỉ hiện dưới md; bảng chỉ hiện từ md trở lên.
    expect($html)
        ->toContain('data-mobile-project-list')
        ->toMatch('/<div[^>]*data-mobile-project-list[^>]*class="[^"]*md:hidden/')
        ->toMatch('/class="[^"]*hidden md:block[^"]*"[^>]*data-desktop-project-table/');

    $list = str($html)->after('data-mobile-project-list')->before('data-desktop-project-table');

    expect((string) $list)
        ->toContain('Công trình Test')
        ->toContain('Thầu Test')
        ->toContain('18,000,000')
        ->toContain(route('projects.invoices.index', $project->id))
        ->toContain(route('invoice.index', ['project_id' => $project->id]));
});
