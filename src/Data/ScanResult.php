<?php

namespace ind4skylivey\LaravelSecureBaseline\Data;

use Illuminate\Support\Collection;
use ind4skylivey\LaravelSecureBaseline\Enums\FindingStatus;

class ScanResult
{
    /**
     * @param  array<int, array{key:string,label:string,findings:array<int, Finding>}>  $groups
     */
    public function __construct(private readonly array $groups)
    {
    }

    /**
     * @return array<int, array{key:string,label:string,findings:array<int, Finding>}> 
     */
    public function groups(): array
    {
        return $this->groups;
    }

    public function findings(): Collection
    {
        return collect($this->groups)
            ->flatMap(fn (array $group) => $group['findings']);
    }

    public function totals(): array
    {
        $counts = [
            FindingStatus::PASS->value => 0,
            FindingStatus::WARNING->value => 0,
            FindingStatus::FAIL->value => 0,
        ];

        foreach ($this->findings() as $finding) {
            $counts[$finding->status->value]++;
        }

        return $counts;
    }

    public function hasFailures(): bool
    {
        return $this->findings()->contains(fn (Finding $finding) => $finding->status === FindingStatus::FAIL);
    }

    public function hasWarnings(): bool
    {
        return $this->findings()->contains(fn (Finding $finding) => $finding->status === FindingStatus::WARNING);
    }

    public function highestStatusFor(array $group): FindingStatus
    {
        $statuses = array_map(fn (Finding $finding) => $finding->status, $group['findings']);

        if (in_array(FindingStatus::FAIL, $statuses, true)) {
            return FindingStatus::FAIL;
        }

        if (in_array(FindingStatus::WARNING, $statuses, true)) {
            return FindingStatus::WARNING;
        }

        return FindingStatus::PASS;
    }
}
