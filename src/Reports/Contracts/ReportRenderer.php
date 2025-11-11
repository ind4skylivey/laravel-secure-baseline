<?php

namespace ind4skylivey\LaravelSecureBaseline\Reports\Contracts;

use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;

interface ReportRenderer
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function render(ScanResult $result, array $context = []): string;

    public function extension(): string;
}
