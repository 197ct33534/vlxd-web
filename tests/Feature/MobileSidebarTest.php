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

it('lets the mobile sheet selection step open the admin sidebar', function () {
    // Bước này chỉ tới được sau khi upload Excel, nên render view trực tiếp.
    $html = view('import-sheet-selection-mobile', [
        'sheetNames' => ['Sheet1', 'Sheet2'],
        'storedFilePath' => 'imports/sample.xlsx',
        'projectId' => null,
    ])->render();

    expect($html)
        ->toContain('$store.layout.toggleSidebar()')
        ->toContain("Alpine.store('layout'")
        ->toContain(route('material-prices.index'));
});
