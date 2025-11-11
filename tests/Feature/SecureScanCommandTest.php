<?php

use Illuminate\Support\Facades\Artisan;
use ind4skylivey\LaravelSecureBaseline\Tests\Stubs\FakeScanner;

it('outputs json summary with totals', function () {
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

    Artisan::call('secure:scan', ['--format' => 'json']);
    $output = Artisan::output();
    $payload = json_decode($output, true);

    expect($payload)
        ->toBeArray()
        ->and($payload['totals']['pass'])->toBeGreaterThan(0)
        ->and($payload['totals']['fail'])->toBeGreaterThan(0)
        ->and(collect($payload['groups'])->pluck('key'))->toContain('fake');
});

it('outputs schema payload with metadata', function () {
    config([
        'secure-baseline.scanners' => ['fake' => true],
        'secure-baseline.custom_scanners' => ['fake' => FakeScanner::class],
    ]);

    Artisan::call('secure:scan', ['--format' => 'schema', '--fail-on' => 'none']);
    $payload = json_decode(Artisan::output(), true);

    expect($payload['tool']['name'])->toBe('Laravel Secure Baseline')
        ->and($payload['groups'][0]['findings'])->not->toBeEmpty();
});

it('outputs sarif payload compatible with security dashboards', function () {
    config([
        'secure-baseline.scanners' => ['fake' => true],
        'secure-baseline.custom_scanners' => ['fake' => FakeScanner::class],
    ]);

    Artisan::call('secure:scan', ['--format' => 'sarif', '--fail-on' => 'none']);
    $payload = json_decode(Artisan::output(), true);

    expect($payload['version'])->toBe('2.1.0')
        ->and($payload['runs'][0]['tool']['driver']['name'])->toBe('Laravel Secure Baseline')
        ->and($payload['runs'][0]['results'])->not->toBeEmpty();
});

it('honors fail-on option and custom exit codes', function () {
    config([
        'secure-baseline.scanners' => ['fake' => true],
        'secure-baseline.custom_scanners' => ['fake' => FakeScanner::class],
    ]);

    $exitCode = Artisan::call('secure:scan', [
        '--format' => 'json',
        '--fail-on' => 'warning',
        '--error-exit-code' => 13,
    ]);

    expect($exitCode)->toBe(13);
});
