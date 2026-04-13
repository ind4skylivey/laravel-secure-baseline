<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Illuminate\Support\Arr;

class CorsScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'cors';
    }

    public function label(): string
    {
        return 'CORS Policy';
    }

    public function scan(): array
    {
        $findings = [];
        $isProduction = $this->app->environment('production');
        $failWildcards = $this->config('cors.fail_wildcards_in_production', true);
        $origins = $this->toArray(config('cors.allowed_origins', ['*']));
        $headers = $this->toArray(config('cors.allowed_headers', ['*']));
        $methods = $this->toArray(config('cors.allowed_methods', ['*']));
        $supportsCredentials = (bool) config('cors.supports_credentials', false);

        $findings[] = $this->evaluateList(
            'Allowed origins',
            $origins,
            $isProduction && $failWildcards
        );

        $findings[] = $this->evaluateList(
            'Allowed headers',
            $headers,
            $isProduction && $failWildcards
        );

        $findings[] = $this->evaluateList(
            'Allowed methods',
            $methods,
            $isProduction && $failWildcards
        );

        if ($supportsCredentials && $this->hasWildcard($origins)) {
            $findings[] = $this->fail(
                'CORS credentials are allowed while origins use "*".',
                'Restrict allowed_origins when supports_credentials=true to avoid exposing cookies across domains.'
            );
        } elseif ($supportsCredentials) {
            $findings[] = $this->pass('CORS credentials allowed with explicit origins.');
        } else {
            $findings[] = $this->pass('CORS credentials disallowed (safer default).');
        }

        if (empty($origins)) {
            $findings[] = $this->warn(
                'No allowed_origins configured.',
                'Explicitly enumerate allowed_origins per environment to avoid implicit wildcards.'
            );
        }

        return $findings;
    }

    private function evaluateList(string $label, array $values, bool $failOnWildcard)
    {
        if ($this->hasWildcard($values)) {
            $message = sprintf('%s contain wildcard "*".', $label);

            return $failOnWildcard
                ? $this->fail($message, 'Restrict CORS wildcards in production to trusted origins.')
                : $this->warn($message, 'Consider listing trusted origins even in non-production environments.');
        }

        return $this->pass(sprintf('%s limited to [%s].', $label, implode(', ', $values) ?: 'none'));
    }

    private function hasWildcard(array $values): bool
    {
        return collect($values)->contains(fn ($value) => trim((string) $value) === '*');
    }

    private function toArray(mixed $value): array
    {
        if (is_string($value)) {
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return array_values(array_filter(array_map('trim', Arr::wrap($value))));
    }
}
