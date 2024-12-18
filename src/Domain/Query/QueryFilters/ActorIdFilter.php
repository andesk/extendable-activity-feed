<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterInterface;

class ActorIdFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $actorId) {}

    public static function createForActor(string|int $actorId): self
    {
        return new self([$actorId]);
    }

    /**
     * @param array<string|int> $actorIds
     */
    public static function createForMultipleActors(array $actorIds): self
    {
        return new self($actorIds);
    }

    /**
     * @return array<string|int>
     */
    public function getActorId(): array
    {
        return $this->actorId;
    }
}
