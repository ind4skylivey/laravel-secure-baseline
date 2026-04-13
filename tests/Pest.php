<?php

require_once __DIR__.'/TestCase.php';

use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toContainFinding', function (string $status, string $needle) {
    $finding = collect($this->value)
        ->first(fn ($item) => str_contains($item->message, $needle) && $item->status->value === $status);

    expect($finding)->not->toBeNull();

    return $this;
});
