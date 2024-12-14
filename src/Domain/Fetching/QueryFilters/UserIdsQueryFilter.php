<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching\QueryFilters;

use Andesk\EAF\Domain\Fetching\QueryFilterInterface;

class UserIdsQueryFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $userIds)
    {
        $this->userIds = $userIds;
    }

    public static function createForSingleUser(string|int $userId): self
    {
        return new self([$userId]);
    }

    /**
     * @param array<string|int> $actorIds
     */
    public static function createForMultipleUsers(array $userIds): self
    {
        return new self($userIds);
    }

    /**
     * @return array<string|int>
     */
    public function getUserIds(): array
    {
        return $this->userIds;
    }
}
