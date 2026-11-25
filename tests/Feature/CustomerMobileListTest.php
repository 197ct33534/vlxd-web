<?php

use App\Models\Customer;
use App\Models\User;

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

it('shows visible customer actions instead of swipe-to-reveal on mobile', function () {
    $customer = Customer::query()->create(['name' => 'Khách Nút', 'phone' => '0944444444']);

    $html = $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get(route('customers.index'))
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
