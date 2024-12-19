<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

/**
 * Interface for resolvers that handle batch relation resolution.
 */
interface RelationsResolverInterface
{
    /**
     * @param array<RelationReference> $refs
     * @return ResolvedReferencesCollection
     */
    public function resolveRelations(array $refs): ResolvedReferencesCollection;
} 