<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterInterface;

class UserPeersFilter implements QueryFilterInterface
{
    private function __construct(private readonly string|int $userId) {}

    public static function createForUser(string|int $userId): self
    {
        return new self($userId);
    }

    /**
     * @return array<string|int>
     */
    public function getUserId(): string|int
    {
        return $this->userId;
    }
}
