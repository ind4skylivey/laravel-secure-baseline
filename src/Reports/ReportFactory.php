<?php

namespace ind4skylivey\LaravelSecureBaseline\Reports;

use InvalidArgumentException;
use ind4skylivey\LaravelSecureBaseline\Reports\Contracts\ReportRenderer;

class ReportFactory
{
    public function make(string $format): ReportRenderer
    {
        return match (strtolower($format)) {
            'md', 'markdown' => new MarkdownReport(),
            'html', 'htm' => new HtmlReport(),
            default => throw new InvalidArgumentException(sprintf('Unsupported report format: %s', $format)),
        };
    }
}
