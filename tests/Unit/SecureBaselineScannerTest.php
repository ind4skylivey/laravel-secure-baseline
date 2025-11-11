<?php

use ind4skylivey\LaravelSecureBaseline\Services\SecureBaselineScanner;
use ind4skylivey\LaravelSecureBaseline\Tests\Stubs\FakeScanner;

it('respects scanner toggles and custom scanners', function () {
    config([
        'secure-baseline.scanners' => [
            'config' => false,
            'session' => false,
            'cors' => false,
            'headers' => false,
            'routes' => false,
            'dependencies' => false,
            'fake' => true,
        ],
        'secure-baseline.custom_scanners' => [
            'fake' => FakeScanner::class,
        ],
    ]);

    $result = resolve(SecureBaselineScanner::class)->scan();

    $groupKeys = collect($result->groups())->pluck('key');

    expect($groupKeys)->toContain('fake')
        ->and($groupKeys)->not->toContain('config');
});
