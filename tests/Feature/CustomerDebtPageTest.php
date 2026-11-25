<?php

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;

it('renders project debt lines as stacked blocks on mobile', function () {
    $customer = Customer::query()->create(['name' => 'Thầu Nợ', 'phone' => '0922222222']);
    $project = Project::query()->create([
        'customer_id' => $customer->id,
        'name' => 'Công trình Nợ',
        'total_invoice' => 5000000,
        'total_paid' => 1000000,
        'total_debt' => 4000000,
    ]);

    $html = $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('admin.customers.debt'))
        ->assertOk()
        ->getContent();

    // Khối mobile chỉ hiện dưới md; bảng chỉ hiện từ md trở lên.
    expect($html)
        ->toMatch('/<div[^>]*data-mobile-debt-projects[^>]*class="[^"]*md:hidden/')
        ->toMatch('/class="[^"]*hidden md:block[^"]*"[^>]*data-desktop-debt-table/');

    $mobile = (string) str($html)->after('data-mobile-debt-projects')->before('data-desktop-debt-table');

    expect($mobile)
        ->toContain('Công trình Nợ')
        ->toContain('4.000.000')
        ->toContain(route('admin.projects.credit', $project))
        ->toContain(route('admin.projects.reconciliation-pdf', $project));
});
