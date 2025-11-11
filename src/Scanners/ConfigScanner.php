<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Illuminate\Support\Str;

class ConfigScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'config';
    }

    public function label(): string
    {
        return 'Configuration';
    }

    public function scan(): array
    {
        $findings = [];
        $appEnv = config('app.env', 'production');
        $isProduction = $this->app->environment('production');
        $appDebug = (bool) config('app.debug');
        $appUrl = (string) config('app.url');
        $appKey = (string) config('app.key');

        if (empty($appKey) || $appKey === 'SomeRandomString') {
            $findings[] = $this->fail(
                'APP_KEY is missing or uses the default placeholder value.',
                'Run `php artisan key:generate` and redeploy to ensure encrypted data remains secure.'
            );
        } else {
            $findings[] = $this->pass('APP_KEY is set.');
        }

        if ($appDebug && $isProduction) {
            $findings[] = $this->fail(
                'APP_DEBUG is enabled while APP_ENV=production.',
                'Disable APP_DEBUG in production to avoid exposing stack traces and secrets.'
            );
        } elseif ($appDebug) {
            $findings[] = $this->warn(
                'APP_DEBUG is enabled outside production.',
                'Keep APP_DEBUG off in any environment that mirrors production traffic.'
            );
        } else {
            $findings[] = $this->pass('APP_DEBUG is disabled.');
        }

        if (empty($appUrl)) {
            $findings[] = $this->warn(
                'APP_URL is not configured.',
                'Set APP_URL to the canonical HTTPS base URL so URL generation and cookies stay consistent.'
            );
        } elseif ($isProduction && ! Str::of($appUrl)->startsWith('https://')) {
            $findings[] = $this->warn(
                sprintf('APP_URL (%s) is not using HTTPS.', $appUrl),
                'Serve production traffic exclusively over HTTPS and update APP_URL accordingly.'
            );
        } else {
            $findings[] = $this->pass(sprintf('APP_URL is set to %s.', $appUrl));
        }

        $logLevel = $this->determineLogLevel();
        if ($isProduction && in_array($logLevel, ['debug', 'info', 'notice'], true)) {
            $findings[] = $this->warn(
                sprintf('Logging level is %s in production.', strtoupper($logLevel)),
                'Switch to warning/error logging in production to reduce sensitive data exposure.'
            );
        } else {
            $findings[] = $this->pass(sprintf('Primary logging channel level is %s.', strtoupper($logLevel)));
        }

        return $findings;
    }

    private function determineLogLevel(): string
    {
        $defaultChannel = config('logging.default', 'stack');
        $level = config("logging.channels.$defaultChannel.level");

        if ($level) {
            return strtolower($level);
        }

        if ($defaultChannel === 'stack') {
            foreach ((array) config('logging.channels.stack.channels', []) as $channel) {
                $channelLevel = config("logging.channels.$channel.level");
                if ($channelLevel) {
                    return strtolower($channelLevel);
                }
            }
        }

        return 'debug';
    }
}
