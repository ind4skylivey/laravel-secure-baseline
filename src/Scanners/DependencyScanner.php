<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Composer\InstalledVersions;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;

class DependencyScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'dependencies';
    }

    public function label(): string
    {
        return 'Dependencies';
    }

    public function scan(): array
    {
        $findings = [];
        $version = $this->currentLaravelVersion();
        $minSupportedMajor = (int) $this->config('dependencies.laravel.min_supported_major', 10);
        $latestKnown = (string) $this->config('dependencies.laravel.latest_known_version');

        if (! $version) {
            return [
                $this->warn('Unable to determine laravel/framework version.', 'Ensure laravel/framework is installed so dependency checks can run.'),
            ];
        }

        $findings[] = $this->pass(sprintf('Laravel/framework %s detected.', $version));

        $major = (int) Str::before($version, '.') ?: 0;
        if ($major > 0 && $major < $minSupportedMajor) {
            $findings[] = $this->fail(
                sprintf('Laravel %s is below the supported major version %d.', $version, $minSupportedMajor),
                sprintf('Upgrade to Laravel %d or newer to stay within the security support window.', $minSupportedMajor)
            );
        } else {
            $findings[] = $this->pass(sprintf('Laravel major version %d meets the supported baseline.', $major));
        }

        if (! empty($latestKnown) && version_compare($version, $latestKnown, '<')) {
            $findings[] = $this->warn(
                sprintf('Laravel %s is behind the latest known %s.', $version, $latestKnown),
                'Review the Laravel release notes and plan a framework upgrade to pick up security fixes.'
            );
        } else {
            $findings[] = $this->pass('Framework is on the latest known release.');
        }

        return $findings;
    }

    private function currentLaravelVersion(): ?string
    {
        if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled('laravel/framework')) {
            return InstalledVersions::getPrettyVersion('laravel/framework');
        }

        if (class_exists(Application::class)) {
            return Application::VERSION;
        }

        return null;
    }
}
