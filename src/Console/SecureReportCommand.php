<?php

namespace ind4skylivey\LaravelSecureBaseline\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use ind4skylivey\LaravelSecureBaseline\Reports\ReportFactory;
use ind4skylivey\LaravelSecureBaseline\Services\SecureBaselineScanner;
use InvalidArgumentException;

class SecureReportCommand extends Command
{
    protected $signature = 'secure:report {--format=md : Report format (md|html)} {--output= : Optional output path or directory}';

    protected $description = 'Generate a Laravel Secure Baseline report without modifying your project files.';

    public function __construct(
        private SecureBaselineScanner $scanner,
        private Filesystem $files,
        private ReportFactory $factory
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('secure-baseline.enabled', true)) {
            $this->components->warn('Secure Baseline scans are disabled via config.');

            return self::SUCCESS;
        }

        $formatOption = $this->option('format') ?: config('secure-baseline.report.default_format', 'md');
        $generatedAt = Carbon::now();

        try {
            $renderer = $this->factory->make($formatOption);
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $result = $this->scanner->scan();
        if (empty($result->groups())) {
            $this->components->warn('No scanners produced findings. Ensure scanners are enabled in config/secure-baseline.php.');

            return self::SUCCESS;
        }

        $content = $renderer->render($result, [
            'generated_at' => $generatedAt,
            'app_env' => config('app.env'),
            'app_url' => config('app.url'),
        ]);

        $path = $this->determineOutputPath($renderer->extension(), (string) $this->option('output'), $generatedAt);
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);

        $this->components->info(sprintf('Secure Baseline %s report saved to %s', strtoupper($formatOption), $path));
        $totals = $result->totals();
        $this->line(sprintf('Findings: %d pass · %d warning · %d fail', $totals['pass'], $totals['warning'], $totals['fail']));

        return self::SUCCESS;
    }

    private function determineOutputPath(string $extension, ?string $option, Carbon $generatedAt): string
    {
        $base = $option ?: config('secure-baseline.report.default_output_path', storage_path('logs/secure-baseline'));
        $base = $base ?: storage_path('logs/secure-baseline');
        $includeTimestamp = (bool) config('secure-baseline.report.include_timestamp', true);

        if ($this->seemsDirectory($base)) {
            $filename = 'secure-baseline-report';
            if ($includeTimestamp) {
                $filename .= '-'.$generatedAt->format('Ymd-His');
            }

            return rtrim($base, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename.'.'.$extension;
        }

        return $base;
    }

    private function seemsDirectory(string $path): bool
    {
        if (empty($path)) {
            return true;
        }

        if (Str::endsWith($path, ['/', '\\'])) {
            return true;
        }

        if ($this->files->isDirectory($path)) {
            return true;
        }

        return ! str_contains(basename($path), '.');
    }
}
