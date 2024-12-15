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
    public function save(BaseActivityInterface $activity): string|int;
    public function delete(string|int $activityId): void;
    public function findById(string|int $id): ?BaseActivityInterface;

    public function getActivities(
        string $feedType,
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null,
        array $queryFilters = []
    ): array;

}