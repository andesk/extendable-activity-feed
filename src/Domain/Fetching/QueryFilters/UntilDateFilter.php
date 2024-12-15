<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching\QueryFilters;

use Andesk\EAF\Domain\Fetching\QueryFilterInterface;

class UntilDateFilter implements QueryFilterInterface
{
    private function __construct(private readonly \DateTimeImmutable $untilDate) {}

    public static function createForUntilDate(\DateTimeImmutable $untilDate): self
    {
        return new self($untilDate);
    }

    public function getUntilDate(): \DateTimeImmutable
    {
        return $this->untilDate;
    }
}
