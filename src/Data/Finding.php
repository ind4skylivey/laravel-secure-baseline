<?php

namespace ind4skylivey\LaravelSecureBaseline\Data;

use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;

class Finding
{
    public function __construct(
        public readonly string $scannerKey,
        public readonly string $scannerLabel,
        public readonly FindingStatus $status,
        public readonly string $message,
        public readonly ?string $recommendation = null,
        public readonly array $meta = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'scanner_key' => $this->scannerKey,
            'scanner_label' => $this->scannerLabel,
            'status' => $this->status->value,
            'message' => $this->message,
            'recommendation' => $this->recommendation,
            'meta' => $this->meta,
        ];
    }
}
