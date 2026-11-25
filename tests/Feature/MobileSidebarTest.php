<?php

beforeEach(function () {
    useMobileUserAgent();
    actingAsAdmin();
});

it('lets the mobile customers page open the admin sidebar', function () {
    $this->get(route('customers.index'))
        ->assertOk()
        ->assertSee('$store.layout.toggleSidebar()', false)
        ->assertSee("Alpine.store('layout'", false)
        ->assertSee(route('material-prices.index'), false);
});

it('lets the mobile import page open the admin sidebar', function () {
    $this->get(route('invoice.index'))
        ->assertOk()
        ->assertSee('$store.layout.toggleSidebar()', false)
        ->assertSee("Alpine.store('layout'", false)
        ->assertSee(route('material-prices.index'), false);
});
