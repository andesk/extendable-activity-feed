<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching\QueryFilters;

use Andesk\EAF\Domain\Fetching\QueryFilterInterface;

class ActorIdQueryFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $actorId)
    {
        $this->actorId = $actorId;
    }

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
