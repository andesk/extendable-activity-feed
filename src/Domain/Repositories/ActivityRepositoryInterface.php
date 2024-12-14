<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Repositories;

use Andesk\EAF\Domain\BaseActivityInterface;
use DateTimeImmutable;

interface ActivityRepositoryInterface
{
    /**
     * @return string|int The id  of the stored activity
     */
    public function persist(BaseActivityInterface $activity, bool $flushToDB = true): string|int;
    public function delete(string|int $activityId, bool $flushToDB = true): void;
    public function findById(string|int $id): ?BaseActivityInterface;

    public function getActivities(
        string $feedType,
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null,
        array $queryFilters = []
    ): array;

}