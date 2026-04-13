<?php

namespace ind4skylivey\LaravelSecureBaseline\Support;

use Composer\InstalledVersions;
use Illuminate\Support\Carbon;
use ind4skylivey\LaravelSecureBaseline\Data\Finding;
use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;

class ResultPayloadBuilder
{
    public static function schema(ScanResult $result, Carbon $generatedAt, array $context = []): array
    {
        return [
            'tool' => [
                'name' => 'Laravel Secure Baseline',
                'version' => self::packageVersion(),
                'homepage' => 'https://github.com/ind4skylivey/laravel-secure-baseline',
            ],
            'generated_at' => $generatedAt->toIso8601String(),
            'environment' => [
                'app_env' => $context['app_env'] ?? null,
                'app_url' => $context['app_url'] ?? null,
            ],
            'totals' => $result->totals(),
            'groups' => array_map(function (array $group) use ($result) {
                return [
                    'key' => $group['key'],
                    'label' => $group['label'],
                    'status' => $result->highestStatusFor($group)->value,
                    'findings' => array_map(fn (Finding $finding) => $finding->toArray(), $group['findings']),
                ];
            }, $result->groups()),
        ];
    }

    public static function sarif(ScanResult $result, Carbon $generatedAt, array $context = [], string $schema = 'https://json.schemastore.org/sarif-2.1.0.json'): array
    {
        $rules = [];
        $sarifResults = [];

        foreach ($result->groups() as $group) {
            $ruleId = 'secure-baseline/'.$group['key'];
            if (! isset($rules[$ruleId])) {
                $rules[$ruleId] = [
                    'id' => $ruleId,
                    'name' => $group['label'],
                    'shortDescription' => ['text' => $group['label']],
                    'helpUri' => 'https://github.com/ind4skylivey/laravel-secure-baseline',
                ];
            }

            foreach ($group['findings'] as $finding) {
                $sarifResults[] = [
                    'ruleId' => $ruleId,
                    'level' => $finding->status->sarifLevel(),
                    'message' => [
                        'text' => $finding->message,
                        'markdown' => trim($finding->message.($finding->recommendation ? "\n\n**Recommendation:** {$finding->recommendation}" : '')),
                    ],
                    'properties' => array_filter([
                        'recommendation' => $finding->recommendation,
                        'meta' => $finding->meta ?: null,
                        'scanner' => $finding->scannerLabel,
                    ]),
                ];
            }
        }

        return [
            '$schema' => $schema,
            'version' => '2.1.0',
            'runs' => [
                [
                    'tool' => [
                        'driver' => [
                            'name' => 'Laravel Secure Baseline',
                            'version' => self::packageVersion(),
                            'informationUri' => 'https://github.com/ind4skylivey/laravel-secure-baseline',
                            'rules' => array_values($rules),
                        ],
                    ],
                    'artifacts' => [],
                    'results' => $sarifResults,
                    'invocations' => [
                        ['executionSuccessful' => ! $result->hasFailures()],
                    ],
                    'conversion' => null,
                    'properties' => [
                        'generatedAt' => $generatedAt->toIso8601String(),
                        'environment' => [
                            'app_env' => $context['app_env'] ?? null,
                            'app_url' => $context['app_url'] ?? null,
                        ],
                        'totals' => $result->totals(),
                    ],
                ],
            ],
        ];
    }

    private static function packageVersion(): string
    {
        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('ind4skylivey/laravel-secure-baseline')) {
            return InstalledVersions::getPrettyVersion('ind4skylivey/laravel-secure-baseline') ?? 'dev-main';
        }

        return 'dev-main';
    }
}
