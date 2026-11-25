<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests chạy trên Tests\TestCase (boot Laravel) + RefreshDatabase.
| phpunit.xml trỏ DB test vào SQLite :memory: nên mỗi lần chạy sẽ migrate
| một DB rỗng, không đụng database.sqlite thật.
|
*/

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->afterEach(function () {
    // Khôi phục User-Agent nếu test đã gọi useMobileUserAgent().
    if (array_key_exists('__pest_original_user_agent', $GLOBALS)) {
        $original = $GLOBALS['__pest_original_user_agent'];
        unset($GLOBALS['__pest_original_user_agent']);

        if ($original === null) {
            unset($_SERVER['HTTP_USER_AGENT']);
        } else {
            $_SERVER['HTTP_USER_AGENT'] = $original;
        }
    }
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations / Helpers
|--------------------------------------------------------------------------
*/

/**
 * Giả lập điện thoại cho các controller chọn view theo Jenssegers\Agent
 * (đọc $_SERVER, không đọc request của test). Tự khôi phục sau mỗi test.
 */
function useMobileUserAgent(): void
{
    if (! array_key_exists('__pest_original_user_agent', $GLOBALS)) {
        $GLOBALS['__pest_original_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Mobile Safari/537.36';
}

function actingAsAdmin(): App\Models\User
{
    $admin = App\Models\User::factory()->create(['role' => 'admin']);
    test()->actingAs($admin);

    return $admin;
}

/**
 * Trang mobile nằm trong layouts.dashboard và không còn khung tự chế cũ.
 */
expect()->extend('toBeDashboardMobilePage', function () {
    $html = (string) $this->value;

    expect($html)
        ->toContain('mf-admin-main')
        ->toContain("Alpine.store('layout'")
        ->not->toContain('CMS Inc')
        ->not->toContain('material-icons-outlined')
        ->not->toContain('relative z-[70]');

    expect(substr_count($html, '<!DOCTYPE html>'))->toBeLessThanOrEqual(1);

    foreach (['Home', 'Clients', 'Invoices', 'Reports', 'Settings', 'Select Sheet to Import', 'Continue Import'] as $english) {
        expect($html)->not->toMatch('/>\s*'.preg_quote($english, '/').'\s*</');
    }

    return $this;
});
