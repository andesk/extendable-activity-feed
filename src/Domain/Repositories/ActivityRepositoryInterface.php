<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Domain\Repositories;

use Andesk\EAF\Domain\BaseActivityInterface;
use DateTimeImmutable;

interface ActivityRepositoryInterface
{
    public function store(BaseActivityInterface $activity, bool $flushToDB = true): void;
    public function delete(string|int $activityId, bool $flushToDB = true): void;
    public function findById(string|int $id): ?BaseActivityInterface;

    public function getFeedForUser(
        string $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array;

    public function getFeedFromUserPeers(
        string $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array;

    public function getFeedFromUserNetwork(
        string $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array;
}