<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

class InMemoryCachingRelationsResolver implements RelationsResolverInterface
{
    private array $cachedRefs = [];
    
    private function __construct(
        private readonly RelationsResolverInterface $decoratedRelationsResolver,
    ) {}

    public function resolveRelations(array $refs): ResolvedReferencesCollection
    {
        $refsToResolve = $refs;

        $unsupportedRefs = [];
        $unresolvedRefs = [];
        $resolvedRefs = [];
        
        if (!empty($this->cachedRefs)) {
            foreach ($refs as $ref) {
                if (isset($this->cachedRefs[$ref->getHashedKey()])) {
                    if ($this->cachedRefs[$ref->getHashedKey()] === null) {
                        $unsupportedRefs[$ref->getHashedKey()] = $ref;
                    } elseif ($this->cachedRefs[$ref->getHashedKey()] === false) {
                        $unresolvedRefs[$ref->getHashedKey()] = $ref;
                    } else {
                        $resolvedRefs[$ref->getType()][$ref->getId()] = $this->cachedRefs[$ref->getHashedKey()];
                    }
                } else {
                    $refsToResolve[] = $ref;
                }
            }
        }

        if (!empty($refsToResolve)) {
            $resolvedCollection = $this->decoratedRelationsResolver->resolveRelations($refsToResolve);
            foreach ($resolvedCollection->getResolvedReferences() as $type => $resolvedRefs) {
                foreach ($resolvedRefs as $id => $resolvedRef) {
                    $resolvedRefs[$type][$id] = $resolvedRef;
                    $this->cachedRefs[$ref->getHashedKey()] = $resolvedRef;
                }
            }
            foreach ($resolvedCollection->getUnresolvedReferences() as $hashedKey => $unresolvedRef) {
                $unresolvedRefs[$hashedKey] = $unresolvedRef;
                $this->cachedRefs[$hashedKey] = false;
            }
            foreach ($resolvedCollection->getUnsupportedReferences() as $hashedKey => $unsupportedRef) {
                $unsupportedRefs[$hashedKey] = $unsupportedRef;
                $this->cachedRefs[$hashedKey] = null;
            }
        }

        $mergedCollection = ResolvedReferencesCollection::create($resolvedRefs, $unresolvedRefs, $unsupportedRefs);

        return $mergedCollection;
    }
}