<?php

namespace ind4skylivey\LaravelSecureBaseline\Contracts;

use ind4skylivey\LaravelSecureBaseline\Data\Finding;

interface Scanner
{
    public function key(): string;

    public function label(): string;

    /**
     * @return array<int, Finding>
     */
    public function scan(): array;
}
