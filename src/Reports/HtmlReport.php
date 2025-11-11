<?php

namespace ind4skylivey\LaravelSecureBaseline\Reports;

use Illuminate\Support\Carbon;
use ind4skylivey\LaravelSecureBaseline\Data\Finding;
use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;
use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;
use ind4skylivey\LaravelSecureBaseline\Reports\Contracts\ReportRenderer;

class HtmlReport implements ReportRenderer
{
    public function render(ScanResult $result, array $context = []): string
    {
        $generatedAt = $context['generated_at'] ?? Carbon::now();
        $appEnv = $context['app_env'] ?? config('app.env');
        $appUrl = $context['app_url'] ?? config('app.url');
        $totals = $result->totals();

        $summaryRows = '';
        foreach ($result->groups() as $group) {
            $status = $result->highestStatusFor($group);
            $summaryRows .= sprintf(
                '<tr><td>%s %s</td><td>%s</td><td>%s</td></tr>',
                $status->icon(),
                $status->label(),
                $this->escape($group['label']),
                $this->escape($this->summarizeGroup($group))
            );
        }

        $detailSections = '';
        foreach ($result->groups() as $group) {
            $status = $result->highestStatusFor($group);
            $detailSections .= sprintf('<section><h2>%s %s</h2>', $status->icon(), $this->escape($group['label']));
            $detailSections .= '<ul>';
            foreach ($group['findings'] as $finding) {
                $detailSections .= '<li>'.$this->formatFinding($finding).'</li>';
            }
            $detailSections .= '</ul></section>';
        }

        $title = 'Laravel Secure Baseline Report';
        $generated = method_exists($generatedAt, 'toDateTimeString') ? $generatedAt->toDateTimeString() : (string) $generatedAt;

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <style>
        body { font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; color:#111; padding:2rem; }
        h1 { margin-bottom:0; }
        table { width:100%; border-collapse: collapse; margin-top:1rem; }
        th, td { border:1px solid #ccc; padding:0.5rem; text-align:left; }
        th { background:#f6f6f6; }
        section { margin-top:2rem; }
        ul { padding-left:1.5rem; }
        .meta { color:#555; margin-top:0.25rem; }
    </style>
</head>
<body>
    <h1>{$title}</h1>
    <p>Generated: {$generated} · Environment: {$appEnv} · App URL: {$appUrl}</p>
    <p>Totals: {$totals['pass']} pass · {$totals['warning']} warning · {$totals['fail']} fail</p>
    <table>
        <thead>
            <tr><th>Status</th><th>Category</th><th>Highlight</th></tr>
        </thead>
        <tbody>
            {$summaryRows}
        </tbody>
    </table>
    {$detailSections}
</body>
</html>
HTML;
    }

    public function extension(): string
    {
        return 'html';
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
        $parts = sprintf('<strong>%s</strong> %s', $finding->status->icon(), $this->escape($finding->message));
        if ($finding->recommendation) {
            $parts .= sprintf('<div class="meta">Recommendation: %s</div>', $this->escape($finding->recommendation));
        }

        if (! empty($finding->meta)) {
            $parts .= sprintf('<div class="meta">Details: %s</div>', $this->escape($this->stringifyMeta($finding->meta)));
        }

        return $parts;
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

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
