<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class HeadersScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'headers';
    }

    public function label(): string
    {
        return 'Security Headers';
    }

    public function scan(): array
    {
        $findings = [];
        $expectations = $this->config('headers.expectations', []);
        $detected = $this->detectedHeaders();
        $critical = $this->config('headers.critical', []);
        $isProduction = $this->app->environment('production');

        if (empty($expectations)) {
            return [
                $this->warn('No security header expectations configured.', 'Update config/secure-baseline.php to list headers you require.'),
            ];
        }

        foreach ($expectations as $header => $expectedValue) {
            $normalizedKey = strtolower($header);
            $actual = $detected[$normalizedKey] ?? null;

            if ($actual === null) {
                if ($expectedValue === null) {
                    $findings[] = $this->warn(
                        sprintf('%s is not configured.', $header),
                        sprintf('Add a %s header via middleware to tighten browser defenses.', $header)
                    );
                    continue;
                }

                $severity = ($isProduction && in_array($header, $critical, true))
                    ? 'fail'
                    : 'warn';

                $findings[] = $this->{$severity}(
                    sprintf('%s header missing.', $header),
                    sprintf('Ensure %s is added to HTTP responses (recommended value: %s).', $header, $expectedValue)
                );

                continue;
            }

            if ($expectedValue && ! $this->valueMatches($actual, $expectedValue)) {
                $findings[] = $this->warn(
                    sprintf('%s is set to "%s" but expected "%s".', $header, $actual, $expectedValue),
                    'Align the header value with the recommended baseline or document the exception.'
                );
            } else {
                $findings[] = $this->pass(sprintf('%s configured (%s).', $header, $actual));
            }
        }

        if (empty($detected)) {
            $findings[] = $this->warn(
                'Unable to detect security headers automatically.',
                'Provide detected headers via secure-baseline.headers.detected or install a secure headers config for automatic discovery.'
            );
        }

        return $findings;
    }

    private function detectedHeaders(): array
    {
        $explicit = $this->config('headers.detected', []);
        if (! empty($explicit)) {
            return $this->normalizeHeaders($explicit);
        }

        $configKey = $this->config('headers.secure_headers_config_key');
        if ($configKey && is_array(config($configKey))) {
            $headers = [];

            if (config("$configKey.x_frame_options.enable")) {
                $headers['X-Frame-Options'] = config("$configKey.x_frame_options.value", 'SAMEORIGIN');
            }

            if (config("$configKey.x_content_type_options.enable")) {
                $headers['X-Content-Type-Options'] = 'nosniff';
            }

            if (config("$configKey.referrer_policy.enable")) {
                $headers['Referrer-Policy'] = config("$configKey.referrer_policy.value", 'no-referrer-when-downgrade');
            }

            if (config("$configKey.hsts.enable")) {
                $maxAge = config("$configKey.hsts.max_age", 31536000);
                $directives = [
                    sprintf('max-age=%d', $maxAge),
                ];

                if (config("$configKey.hsts.include_sub_domains")) {
                    $directives[] = 'includeSubDomains';
                }

                if (config("$configKey.hsts.preload")) {
                    $directives[] = 'preload';
                }

                $headers['Strict-Transport-Security'] = implode('; ', $directives);
            }

            if (config("$configKey.csp.enable") && is_array(config("$configKey.csp.directives"))) {
                $headers['Content-Security-Policy'] = $this->stringifyCsp(config("$configKey.csp.directives"));
            }

            return $this->normalizeHeaders($headers);
        }

        return [];
    }

    private function normalizeHeaders(array $headers): array
    {
        $normalized = [];
        foreach ($headers as $name => $value) {
            $normalized[strtolower((string) $name)] = $value;
        }

        return $normalized;
    }

    private function stringifyCsp(array $directives): string
    {
        $parts = [];
        foreach ($directives as $directive => $value) {
            $valueString = is_array($value) ? implode(' ', $value) : (string) $value;
            $parts[] = trim(sprintf('%s %s', $directive, $valueString));
        }

        return implode('; ', array_filter($parts));
    }

    private function valueMatches(string $actual, string $expected): bool
    {
        $actual = Str::lower(trim($actual));
        $expected = Str::lower(trim($expected));

        return $actual === $expected || str_contains($actual, $expected);
    }
}
