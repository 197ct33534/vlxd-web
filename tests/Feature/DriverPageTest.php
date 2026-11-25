<?php

use App\Models\StoreInfo;
use App\Models\User;

// CTA Gọi/Zalo chỉ render khi cửa hàng có SĐT hoặc Zalo.
beforeEach(function () {
    StoreInfo::query()->create(['name' => 'Cửa hàng test', 'phone' => '0900000000']);
});

it('does not stack the store CTA under the driver bottom nav', function () {
    $driver = User::factory()->create(['role' => 'driver']);

    $html = $this->actingAs($driver)
        ->get(route('driver.index'))
        ->assertOk()
        ->getContent();

    // Trang tài xế có thanh điều hướng riêng: body phải chừa chỗ cho nó và
    // không hiện CTA dính đáy (header đã có nút Gọi/Zalo).
    expect($html)
        ->toMatch('/<body[^>]*\bhas-portal-nav\b/')
        ->not->toContain('mf-sticky-cta');
});

it('still shows the store CTA to guests on the portal login', function () {
    $this->get(route('portal.login'))
        ->assertOk()
        ->assertSee('mf-sticky-cta', false);
});
