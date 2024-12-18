<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

/**
 * Interface for resolvers that handle single object resolution.
 */
interface SingleObjectResolverInterface
{
    public function resolve(ObjectReference $ref): ?object;
} 