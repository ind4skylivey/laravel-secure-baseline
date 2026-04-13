<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use ind4skylivey\LaravelSecureBaseline\Tests\Stubs\FakeScanner;

it('writes a markdown report to disk', function () {
    $path = storage_path('framework/testing/secure-baseline-report.md');
    File::delete($path);

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
        'secure-baseline.report.include_timestamp' => false,
    ]);

    Artisan::call('secure:report', ['--format' => 'md', '--output' => $path]);

    expect(File::exists($path))->toBeTrue();
    $contents = File::get($path);
    expect($contents)
        ->toContain('Laravel Secure Baseline Report')
        ->and($contents)->toContain('Fake Scanner');
});
