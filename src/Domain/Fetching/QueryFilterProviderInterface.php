<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching;

use DateTimeImmutable;

interface QueryFilterProviderInterface
{
    public function expand(array $queryFilters, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate): array;
}