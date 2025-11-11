<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Illuminate\Support\Str;

class SessionScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'session';
    }

    public function label(): string
    {
        return 'Sessions & Cookies';
    }

    public function scan(): array
    {
        $findings = [];
        $isHttpsApp = $this->appUrlIsHttps();
        $sessionSecure = config('session.secure');
        $sessionHttpOnly = config('session.http_only', true);
        $sameSite = config('session.same_site');
        $driver = (string) config('session.driver', 'file');
        $isProduction = $this->app->environment('production');

        if ($isHttpsApp && $sessionSecure !== true) {
            $findings[] = $this->fail(
                'SESSION_SECURE_COOKIE is not enabled for an HTTPS application.',
                'Set SESSION_SECURE_COOKIE=true to prevent browsers from sending cookies over unsecured connections.'
            );
        } else {
            $findings[] = $this->pass($sessionSecure ? 'Secure cookies are enforced.' : 'Secure cookies allowed on HTTP (expected in local dev).');
        }

        if (! $sessionHttpOnly) {
            $findings[] = $this->fail(
                'SESSION_HTTP_ONLY is disabled.',
                'Enable HttpOnly cookies to block JavaScript access and reduce XSS impact.'
            );
        } else {
            $findings[] = $this->pass('HttpOnly session cookies enabled.');
        }

        if (empty($sameSite)) {
            $findings[] = $this->warn(
                'SESSION_SAME_SITE is not configured.',
                'Set SESSION_SAME_SITE=lax or strict to mitigate CSRF. Use SameSite=None only for cross-site scenarios with HTTPS.'
            );
        } else {
            $normalizedSameSite = Str::lower($sameSite);
            if ($normalizedSameSite === 'none' && $sessionSecure !== true) {
                $findings[] = $this->fail(
                    'SESSION_SAME_SITE=none without secure cookies allows third-party leakage.',
                    'Combine SameSite=None with SESSION_SECURE_COOKIE=true.'
                );
            } else {
                $findings[] = $this->pass(sprintf('SESSION_SAME_SITE=%s.', Str::upper($normalizedSameSite)));
            }
        }

        if ($isProduction && in_array($driver, ['array', 'cookie'], true)) {
            $findings[] = $this->warn(
                sprintf('Session driver "%s" is not recommended for production.', $driver),
                'Use redis, database, memcached, or dynamodb drivers for durability and rotation.'
            );
        } else {
            $findings[] = $this->pass(sprintf('Session driver set to %s.', $driver));
        }

        return $findings;
    }

    private function appUrlIsHttps(): bool
    {
        $url = (string) config('app.url');

        return Str::startsWith(Str::lower($url), 'https://');
    }
}
