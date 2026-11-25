<?php

use App\Models\Customer;

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
