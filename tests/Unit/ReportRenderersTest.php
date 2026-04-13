<?php

use Illuminate\Support\Carbon;
use ind4skylivey\LaravelSecureBaseline\Data\Finding;
use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;
use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;
use ind4skylivey\LaravelSecureBaseline\Reports\HtmlReport;
use ind4skylivey\LaravelSecureBaseline\Reports\MarkdownReport;

function sampleScanResult(): ScanResult
{
    return new ScanResult([
        [
            'key' => 'config',
            'label' => 'Configuration',
            'findings' => [
                new Finding('config', 'Configuration', FindingStatus::FAIL, 'APP_KEY missing'),
                new Finding('config', 'Configuration', FindingStatus::WARNING, 'APP_URL not https'),
            ],
        ],
    ]);
}

it('renders markdown report with headings and totals', function () {
    $report = (new MarkdownReport())->render(sampleScanResult(), [
        'generated_at' => Carbon::parse('2025-01-01 00:00:00'),
        'app_env' => 'testing',
    ]);

    expect($report)
        ->toContain('# Laravel Secure Baseline Report')
        ->and($report)->toContain('APP_KEY missing');
});

it('renders html report with summary table', function () {
    $html = (new HtmlReport())->render(sampleScanResult(), [
        'generated_at' => Carbon::parse('2025-01-01 00:00:00'),
        'app_env' => 'production',
    ]);

    expect($html)
        ->toContain('<table>')
        ->and($html)->toContain('APP_KEY missing')
        ->and($html)->toContain('Totals:');
});
