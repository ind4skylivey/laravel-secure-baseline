<?php

namespace ind4skylivey\LaravelSecureBaseline\Reports;

use Illuminate\Support\Carbon;
use ind4skylivey\LaravelSecureBaseline\Data\Finding;
use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;
use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;
use ind4skylivey\LaravelSecureBaseline\Reports\Contracts\ReportRenderer;

class MarkdownReport implements ReportRenderer
{
    public function render(ScanResult $result, array $context = []): string
    {
        $generatedAt = $context['generated_at'] ?? Carbon::now();
        $appEnv = $context['app_env'] ?? config('app.env');
        $appUrl = $context['app_url'] ?? config('app.url');

        $lines = [];
        $lines[] = '# Laravel Secure Baseline Report';
        $lines[] = sprintf('*Generated:* %s', $this->formatDate($generatedAt));
        $lines[] = sprintf('*Environment:* %s', $appEnv ?: 'unknown');
        if ($appUrl) {
            $lines[] = sprintf('*App URL:* %s', $appUrl);
        }
        $lines[] = '';

        $lines[] = '## Summary';
        $lines[] = '| Status | Category | Highlight |';
        $lines[] = '| --- | --- | --- |';
        foreach ($result->groups() as $group) {
            $status = $result->highestStatusFor($group);
            $lines[] = sprintf('| %s %s | %s | %s |', $status->icon(), $status->label(), $group['label'], $this->summarizeGroup($group));
        }
        $lines[] = '';

        $totals = $result->totals();
        $lines[] = sprintf('Totals: %d pass · %d warning · %d fail', $totals['pass'], $totals['warning'], $totals['fail']);
        $lines[] = '';

        $lines[] = '## Detailed Findings';
        foreach ($result->groups() as $group) {
            $status = $result->highestStatusFor($group);
            $lines[] = sprintf('### %s %s', $status->icon(), $group['label']);
            foreach ($group['findings'] as $finding) {
                $lines[] = $this->formatFinding($finding);
            }
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    public function extension(): string
    {
        return 'md';
    }

    private function formatDate($value): string
    {
        return method_exists($value, 'toDateTimeString')
            ? $value->toDateTimeString()
            : (string) $value;
    }

    private function summarizeGroup(array $group): string
    {
        $priority = [FindingStatus::FAIL, FindingStatus::WARNING, FindingStatus::PASS];

        foreach ($priority as $status) {
            foreach ($group['findings'] as $finding) {
                if ($finding->status === $status) {
                    return $finding->message;
                }
            }
        }

        return 'No findings recorded.';
    }

    private function formatFinding(Finding $finding): string
    {
        $line = sprintf('- %s %s', $finding->status->icon(), $finding->message);
        $details = [];

        if ($finding->recommendation) {
            $details[] = sprintf('Recommendation: %s', $finding->recommendation);
        }

        if (! empty($finding->meta)) {
            $details[] = sprintf('Details: %s', $this->stringifyMeta($finding->meta));
        }

        if ($details) {
            foreach ($details as $detail) {
                $line .= sprintf("\n  - %s", $detail);
            }
        }

        return $line;
    }

    private function stringifyMeta(array $meta): string
    {
        return collect($meta)
            ->map(fn ($value, $key) => sprintf('%s=%s', $key, $this->normalizeValue($value)))
            ->implode(', ');
    }

    private function normalizeValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_array($value)) {
            return '['.implode(', ', array_map(fn ($item) => $this->normalizeValue($item), $value)).']';
        }

        return (string) $value;
    }
}
