<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Repositories;

use Andesk\EAF\Domain\BaseActivityInterface;
use DateTimeImmutable;

interface ExtendedActivityRepositoryInterface extends ActivityRepositoryInterface
{
    public function getFeedFromUserPeers(
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array;

    public function getFeedFromUserNetwork(
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array;
}