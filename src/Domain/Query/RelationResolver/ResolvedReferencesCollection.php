<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

class ResolvedReferencesCollection
{
    private function __construct(
        private array $resolvedReferences = [],
        private array $unresolvedReferences = [],
        private array $unsupportedReferences = []
    ) {}

    public static function create(array $resolvedRefs, array $unresolvedRefs, array $unsupportedRefs): self
    {
        return new self($resolvedRefs, $unresolvedRefs, $unsupportedRefs);
    }

    /**
     * Array of resolved references, keyed by resolved content type and content id.
     * @return array<string, array<string|int,object|array>
     */
    public function getResolvedReferences(): array
    {
        return $this->resolvedReferences;
    }

    /**
     * Array of unresolved references, keyed by reference hashed key.
     * @return array<string, RelationReference>
     */
    public function getUnresolvedReferences(): array
    {
        return $this->unresolvedReferences;
    }

    /**
     * @return array<string, RelationReference>
     */
    public function getUnsupportedReferences(): array
    {
        return $this->unsupportedReferences;
    }
}