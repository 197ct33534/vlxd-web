<?php

use App\Models\Customer;

it('renders the mobile customers page inside the dashboard shell', function () {
    useMobileUserAgent();
    actingAsAdmin();
    Customer::query()->create(['name' => 'Khách Khung', 'phone' => '0955555555']);

    $html = $this->get(route('customers.index'))->assertOk()->getContent();

    expect($html)
        ->toBeDashboardMobilePage()
        ->toContain('openAdd()')
        ->toContain('data-customer-card')
        ->toContain('@submit="isLoading = true"');
});

it('keeps the desktop customers view on desktop', function () {
    actingAsAdmin();
    Customer::query()->create(['name' => 'Khách Desktop', 'phone' => '0966666666']);

    $this->get(route('customers.index'))
        ->assertOk()
        ->assertDontSee('data-customer-card', false);
});

it('shows visible customer actions instead of swipe-to-reveal on mobile', function () {
    useMobileUserAgent();
    actingAsAdmin();
    $customer = Customer::query()->create(['name' => 'Khách Nút', 'phone' => '0944444444']);

    $html = $this->get(route('customers.index'))
        ->assertOk()
        ->getContent();

    // Không còn cử chỉ vuốt.
    expect($html)
        ->not->toContain('handleTouchMove')
        ->not->toContain('swipe_left');

    $card = (string) str($html)->after('data-customer-card="'.$customer->id.'"')->before('</article>');

    expect($card)
        ->toContain('Khách Nút')
        ->toContain(route('customers.projects.index', $customer->id))
        ->toContain('openEdit(')
        ->toContain("openDelete('".route('customers.destroy', $customer->id)."')");
});
