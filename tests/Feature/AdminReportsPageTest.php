<?php

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;

it('renders the project debt report as cards on mobile', function () {
    $customer = Customer::query()->create(['name' => 'Thầu Báo Cáo', 'phone' => '0933333333']);
    Project::query()->create([
        'customer_id' => $customer->id,
        'name' => 'Công trình Báo Cáo',
        'total_invoice' => 7000000,
        'total_paid' => 2000000,
        'total_debt' => 5000000,
    ]);

    $html = $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->getContent();

    // Thẻ chỉ hiện dưới md; bảng 800px chỉ hiện từ md trở lên.
    expect($html)
        ->toMatch('/<div[^>]*data-mobile-report-projects[^>]*class="[^"]*md:hidden/')
        ->toMatch('/class="[^"]*hidden md:block[^"]*"[^>]*data-desktop-report-table/');

    $mobile = (string) str($html)->after('data-mobile-report-projects')->before('data-desktop-report-table');

    expect($mobile)
        ->toContain('Công trình Báo Cáo')
        ->toContain('Thầu Báo Cáo')
        ->toContain('7.000.000')
        ->toContain('2.000.000')
        ->toContain('5.000.000');
});
