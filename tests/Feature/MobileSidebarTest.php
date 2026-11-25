<?php

use App\Models\User;

// Jenssegers\Agent đọc User-Agent từ $_SERVER, không từ request của test.
beforeEach(function () {
    $this->originalUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Mobile Safari/537.36';
});

afterEach(function () {
    if ($this->originalUserAgent === null) {
        unset($_SERVER['HTTP_USER_AGENT']);
    } else {
        $_SERVER['HTTP_USER_AGENT'] = $this->originalUserAgent;
    }
});

it('lets the mobile customers page open the admin sidebar', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('customers.index'))
        ->assertOk()
        ->assertSee('$store.layout.toggleSidebar()', false)
        ->assertSee("Alpine.store('layout'", false)
        ->assertSee(route('material-prices.index'), false);
});

it('lets the mobile import page open the admin sidebar', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('invoice.index'))
        ->assertOk()
        ->assertSee('$store.layout.toggleSidebar()', false)
        ->assertSee("Alpine.store('layout'", false)
        ->assertSee(route('material-prices.index'), false);
});

it('lets the mobile sheet selection step open the admin sidebar', function () {
    // Bước này chỉ tới được sau khi upload Excel, nên render view trực tiếp.
    $this->actingAs(User::factory()->create(['role' => 'admin']));

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
