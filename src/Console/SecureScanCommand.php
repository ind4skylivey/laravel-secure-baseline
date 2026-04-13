<?php

namespace ind4skylivey\LaravelSecureBaseline\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use ind4skylivey\LaravelSecureBaseline\Data\Finding;
use ind4skylivey\LaravelSecureBaseline\Data\ScanResult;
use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;
use ind4skylivey\LaravelSecureBaseline\Services\SecureBaselineScanner;
use ind4skylivey\LaravelSecureBaseline\Support\ResultPayloadBuilder;

class SecureScanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'secure:scan
        {--format=cli : Output format (cli|json|schema|sarif)}
        {--fail-on= : Severity threshold for non-zero exit (none|warning|fail)}
        {--error-exit-code= : Exit code when the threshold is met}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Laravel Secure Baseline checks and print a summary report.';

    public function __construct(private SecureBaselineScanner $scanner)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('secure-baseline.enabled', true)) {
            $this->components->warn('Secure Baseline scans are disabled via config.');

            return self::SUCCESS;
        }

        $format = strtolower($this->option('format') ?? 'cli');
        $failOn = strtolower($this->option('fail-on') ?? config('secure-baseline.cli.fail_on', 'fail'));
        $errorExitCode = (int) ($this->option('error-exit-code') ?? config('secure-baseline.cli.error_exit_code', 2));
        $result = $this->scanner->scan();
        $generatedAt = Carbon::now();

        $context = [
            'app_env' => config('app.env'),
            'app_url' => config('app.url'),
        ];

        switch ($format) {
            case 'cli':
                $this->renderCli($result);
                break;
            case 'json':
            case 'schema':
                $payload = ResultPayloadBuilder::schema($result, $generatedAt, $context);
                $this->renderStructured($payload);
                break;
            case 'sarif':
                $schema = config('secure-baseline.cli.sarif_schema', 'https://json.schemastore.org/sarif-2.1.0.json');
                $payload = ResultPayloadBuilder::sarif($result, $generatedAt, $context, $schema);
                $this->renderStructured($payload);
                break;
            default:
                $this->components->error(sprintf('Unsupported format "%s".', $format));

                return self::FAILURE;
        }

        return $this->determineExitCode($result, $failOn, $errorExitCode);
    }

    private function renderCli(ScanResult $result): void
    {
        $groups = $result->groups();

        if (empty($groups)) {
            $this->components->warn('No scanners are currently enabled. Check config/secure-baseline.php.');
            return;
        }

        $this->components->info('Laravel Secure Baseline');
        $this->line(str_repeat('─', 64));

        foreach ($groups as $group) {
            $status = $result->highestStatusFor($group);
            $summary = $this->summarizeGroup($group);
            $this->line(sprintf('%s %-24s %s', $status->icon(), $group['label'], $summary));
        }

        $totals = $result->totals();
        $this->line(str_repeat('─', 64));
        $this->line(sprintf('Summary: %d pass • %d warn • %d fail', $totals['pass'], $totals['warning'], $totals['fail']));
    }

    private function renderStructured(array $payload): void
    {
        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param  array{findings: array<int, Finding>}  $group
     */
    private function summarizeGroup(array $group): string
    {
        $priority = [
            FindingStatus::FAIL,
            FindingStatus::WARNING,
            FindingStatus::PASS,
        ];

        foreach ($priority as $status) {
            foreach ($group['findings'] as $finding) {
                if ($finding->status === $status) {
                    return $finding->message;
                }
            }
        }

        return 'No findings recorded.';
    }

    private function determineExitCode(ScanResult $result, string $failOn, int $errorExitCode): int
    {
        $value = match ($failOn) {
            'none' => false,
            'warning', 'warn' => $result->hasWarnings() || $result->hasFailures(),
            'fail', 'failure' => $result->hasFailures(),
            default => null,
        };

        if (is_null($value)) {
            $this->components->warn(sprintf('Unknown fail-on value "%s"; defaulting to "fail".', $failOn));
            $value = $result->hasFailures();
        }

        return $value ? $errorExitCode : self::SUCCESS;
    }
}
