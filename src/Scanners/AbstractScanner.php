<?php

namespace ind4skylivey\LaravelSecureBaseline\Scanners;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use ind4skylivey\LaravelSecureBaseline\Contracts\Scanner;
use ind4skylivey\LaravelSecureBaseline\Data\Finding;
use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;

abstract class AbstractScanner implements Scanner
{
    public function __construct(
        protected Application $app,
        protected array $packageConfig = []
    ) {
    }

    abstract public function key(): string;

    abstract public function label(): string;

    protected function config(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->packageConfig, $key, $default);
    }

    protected function finding(FindingStatus $status, string $message, ?string $recommendation = null, array $meta = []): Finding
    {
        return new Finding($this->key(), $this->label(), $status, $message, $recommendation, $meta);
    }

    protected function pass(string $message, ?string $recommendation = null, array $meta = []): Finding
    {
        return $this->finding(FindingStatus::PASS, $message, $recommendation, $meta);
    }

    protected function warn(string $message, ?string $recommendation = null, array $meta = []): Finding
    {
        return $this->finding(FindingStatus::WARNING, $message, $recommendation, $meta);
    }

    protected function fail(string $message, ?string $recommendation = null, array $meta = []): Finding
    {
        return $this->finding(FindingStatus::FAIL, $message, $recommendation, $meta);
    }
}
