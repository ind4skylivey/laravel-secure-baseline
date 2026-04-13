<?php

namespace ind4skylivey\LaravelSecureBaseline;

use Illuminate\Support\ServiceProvider;
use ind4skylivey\LaravelSecureBaseline\Console\SecureReportCommand;
use ind4skylivey\LaravelSecureBaseline\Console\SecureScanCommand;
use ind4skylivey\LaravelSecureBaseline\Services\SecureBaselineScanner;

class SecureBaselineServiceProvider extends ServiceProvider
{
    /**
     * Register bindings and merge package configuration.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/secure-baseline.php', 'secure-baseline');

        $this->app->singleton(SecureBaselineScanner::class, function ($app) {
            return new SecureBaselineScanner($app, $app['config']->get('secure-baseline', []));
        });

        $this->app->alias(SecureBaselineScanner::class, 'secure-baseline.scanner');
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/secure-baseline.php' => config_path('secure-baseline.php'),
        ], 'secure-baseline-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SecureScanCommand::class,
                SecureReportCommand::class,
            ]);
        }
    }
}
