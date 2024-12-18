<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterInterface;

class UserIdsFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $userIds) {}

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
