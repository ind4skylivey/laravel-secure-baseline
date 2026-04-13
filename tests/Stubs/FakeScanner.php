<?php

namespace ind4skylivey\LaravelSecureBaseline\Tests\Stubs;

use ind4skylivey\LaravelSecureBaseline\Scanners\AbstractScanner;

class FakeScanner extends AbstractScanner
{
    public function key(): string
    {
        return 'fake';
    }

    public function label(): string
    {
        return 'Fake Scanner';
    }

    public function scan(): array
    {
        return [
            $this->pass('Fake baseline check passed.'),
            $this->warn('Fake caution raised.', 'Monitor this fake warning.'),
            $this->fail('Fake critical issue detected.', 'Resolve the fake issue immediately.'),
        ];
    }
}
