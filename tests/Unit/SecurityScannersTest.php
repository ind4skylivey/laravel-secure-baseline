<?php

use Illuminate\Support\Facades\Route;
use ind4skylivey\LaravelSecureBaseline\Scanners\ConfigScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\SessionScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\CorsScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\HeadersScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\RoutesScanner;

it('fails when APP_KEY is missing in production', function () {
    config(['app.key' => null]);
    $this->setAppEnvironment('production');

    $findings = $this->runScanner(ConfigScanner::class);

    expect($findings)->toContainFinding('fail', 'APP_KEY is missing');
});

it('flags insecure session cookies on https apps', function () {
    config([
        'app.url' => 'https://secure.example',
        'session.secure' => false,
    ]);

    $this->setAppEnvironment('production');

    $findings = $this->runScanner(SessionScanner::class);

    expect($findings)->toContainFinding('fail', 'SESSION_SECURE_COOKIE is not enabled');
});

it('fails on wildcard CORS origins in production', function () {
    config([
        'cors.allowed_origins' => ['*'],
        'cors.allowed_headers' => ['*'],
        'cors.allowed_methods' => ['*'],
    ]);

    $this->setAppEnvironment('production');

    $findings = $this->runScanner(CorsScanner::class);

    expect($findings)->toContainFinding('fail', 'Allowed origins contain wildcard');
});

it('fails when critical security headers are missing', function () {
    $this->setAppEnvironment('production');

    $findings = $this->runScanner(HeadersScanner::class, [
        'headers' => [
            'expectations' => [
                'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains; preload',
            ],
            'detected' => [],
            'critical' => ['Strict-Transport-Security'],
        ],
    ]);

    expect($findings)->toContainFinding('fail', 'Strict-Transport-Security header missing');
});

it('flags telescope route without auth middleware', function () {
    Route::get('/telescope', fn () => 'ok');
    $this->setAppEnvironment('production');

    $findings = $this->runScanner(RoutesScanner::class);

    expect($findings)->toContainFinding('fail', '/telescope route detected');
});

it('detects protected routes even when middleware uses different casing', function () {
    Route::middleware(\Illuminate\Auth\Middleware\Authenticate::class)
        ->get('/admin-panel', fn () => 'ok');

    $this->setAppEnvironment('production');

    $findings = $this->runScanner(RoutesScanner::class, [
        'routes' => [
            'sensitive_paths' => ['/admin-panel'],
        ],
    ]);

    expect($findings)->toContainFinding('pass', 'Protected by middleware');
});
