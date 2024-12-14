<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching;
use Andesk\EAF\Domain\Activity;
use DateTimeImmutable;

interface PostProcessorInterface
{
    /**
     * @param Activity[] $activities
     * @return Activity[]
     */
    public function process(array $activities, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate, array $queryFilters): array;
}
