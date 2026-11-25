<?php

use PHPUnit\Framework\ExpectationFailedException;

$shell = '<!DOCTYPE html><main class="mf-admin-main"></main><script>Alpine.store(\'layout\', {})</script>';

it('accepts a page rendered inside the dashboard shell', function () use ($shell) {
    expect($shell)->toBeDashboardMobilePage();
});

it('rejects leftover standalone mobile chrome', function (string $extra) use ($shell) {
    expect(fn () => expect($shell.$extra)->toBeDashboardMobilePage())
        ->toThrow(ExpectationFailedException::class);
})->with([
    'brand' => '<span>CMS Inc.</span>',
    'old icon font' => '<span class="material-icons-outlined">menu</span>',
    'standalone sidebar' => '<div class="relative z-[70]"></div>',
    'english nav' => '<span> Settings </span>',
    'second document' => '<!DOCTYPE html>',
]);

it('rejects a page without the dashboard shell', function () {
    expect(fn () => expect('<html><body>x</body></html>')->toBeDashboardMobilePage())
        ->toThrow(ExpectationFailedException::class);
});

it('sets and restores the mobile user agent', function () {
    useMobileUserAgent();

    expect((new Jenssegers\Agent\Agent())->isMobile())->toBeTrue();
});

it('leaves no mobile user agent behind for the next test', function () {
    expect((new Jenssegers\Agent\Agent())->isMobile())->toBeFalse();
});
