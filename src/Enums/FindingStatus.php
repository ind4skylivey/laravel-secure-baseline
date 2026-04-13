<?php

namespace ind4skylivey\LaravelSecureBaseline\Enums;

enum FindingStatus: string
{
    case PASS = 'pass';
    case WARNING = 'warning';
    case FAIL = 'fail';

    public function icon(): string
    {
        return match ($this) {
            self::PASS => '✅',
            self::WARNING => '⚠️',
            self::FAIL => '❌',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PASS => 'Pass',
            self::WARNING => 'Warning',
            self::FAIL => 'Fail',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::FAIL => 3,
            self::WARNING => 2,
            self::PASS => 1,
        };
    }

    public function sarifLevel(): string
    {
        return match ($this) {
            self::FAIL => 'error',
            self::WARNING => 'warning',
            self::PASS => 'note',
        };
    }
}
