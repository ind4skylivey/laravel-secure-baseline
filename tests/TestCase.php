<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use ind4skylivey\LaravelSecureBaseline\SecureBaselineServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [SecureBaselineServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:QWxhZGRpbjpPcGVuU2VzYW1lS2V5LXNlY3VyZQ==');
        $app['config']->set('app.debug', false);
        $app['config']->set('app.url', 'https://example.test');
        $app['config']->set('app.env', 'testing');
        $app['config']->set('session.secure', true);
        $app['config']->set('session.http_only', true);
        $app['config']->set('session.same_site', 'lax');
        $app['config']->set('session.driver', 'file');
        $app['config']->set('logging.default', 'stack');
        $app['config']->set('logging.channels.stack', [
            'driver' => 'stack',
            'channels' => ['single'],
        ]);
        $app['config']->set('logging.channels.single', [
            'driver' => 'single',
            'level' => 'warning',
        ]);
    }

    protected function setAppEnvironment(string $environment): void
    {
        $this->app->detectEnvironment(fn () => $environment);
        $this->app['env'] = $environment;
        $this->app['config']->set('app.env', $environment);
        $_ENV['APP_ENV'] = $environment;
        putenv('APP_ENV='.$environment);
    }

    protected function runScanner(string $scannerClass, array $configOverrides = []): array
    {
        $packageConfig = array_replace_recursive(
            $this->app['config']->get('secure-baseline', []),
            $configOverrides
        );

        return $this->app->make($scannerClass, ['packageConfig' => $packageConfig])->scan();
    }
}
