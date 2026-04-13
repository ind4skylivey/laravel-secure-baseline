<?php

namespace ind4skylivey\LaravelSecureBaseline\Services;

use Illuminate\Contracts\Foundation\Application;
use ind4skylivey\LaravelSecureBaseline\Contracts\Scanner;
use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;
use ind4skylivey\LaravelSecureBaseline\Scanners\ConfigScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\CorsScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\DependencyScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\HeadersScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\RoutesScanner;
use ind4skylivey\LaravelSecureBaseline\Scanners\SessionScanner;

class SecureBaselineScanner
{
    /**
     * @var array<string, class-string<Scanner>>
     */
    protected array $defaultScannerMap = [
        'config' => ConfigScanner::class,
        'session' => SessionScanner::class,
        'cors' => CorsScanner::class,
        'headers' => HeadersScanner::class,
        'routes' => RoutesScanner::class,
        'dependencies' => DependencyScanner::class,
    ];

    public function __construct(
        protected Application $app,
        protected array $packageConfig
    ) {
    }

    public function scan(): ScanResult
    {
        $groups = [];
        $scannerConfig = $this->packageConfig['scanners'] ?? [];
        $scannerMap = array_merge($this->defaultScannerMap, $this->packageConfig['custom_scanners'] ?? []);

        foreach ($scannerMap as $key => $scannerClass) {
            if (! is_subclass_of($scannerClass, Scanner::class)) {
                continue;
            }

            $enabled = $scannerConfig[$key] ?? true;

            if (! $enabled) {
                continue;
            }

            /** @var Scanner $scanner */
            $scanner = $this->app->make($scannerClass, [
                'packageConfig' => $this->packageConfig,
            ]);

            $findings = $scanner->scan();

            $groups[] = [
                'key' => $key,
                'label' => $scanner->label(),
                'findings' => $findings,
            ];
        }

        return new ScanResult($groups);
    }
}
