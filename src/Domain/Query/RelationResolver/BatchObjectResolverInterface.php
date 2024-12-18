<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

/**
 * Interface for resolvers that handle batch object resolution.
 */
interface BatchObjectResolverInterface
{
    /**
     * @param array<ObjectReference> $refs
     * @return array<string, object> indexed by reference hash
     */
    public function resolveBatch(array $refs): array;
} 