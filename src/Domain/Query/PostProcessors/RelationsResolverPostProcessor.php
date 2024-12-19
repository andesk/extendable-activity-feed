<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\PostProcessors;

use Andesk\EAF\Domain\Query\PostProcessorInterface;
use Andesk\EAF\Domain\Query\RelationResolver\RelationReference;
use Andesk\EAF\Domain\Query\RelationResolver\RelationsResolverInterface;
use Andesk\EAF\Domain\Query\RelationResolver\ResolvedReferencesCollection;
use Andesk\EAF\Domain\RelationsResolvableActivityInterface;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Post-processor that handles the resolution of activity relations through multiple phases:
 * - Pre-batch phase for optimized bulk loading
 * - Single resolution phase for individual objects
 * - Post-batch phase for optimization and denormalization
 */
class RelationsResolverPostProcessor implements PostProcessorInterface
{
    private array $relationsResolvers = [];
    public function addRelationsResolver(RelationsResolverInterface $relationsResolver, $priority = 500): void
    {
        if (isset($this->relationsResolvers[$priority])) {
            throw new InvalidArgumentException(
                sprintf('A resolver with priority %d already exists', $priority)
            );
        }
        
        $this->relationsResolvers[$priority] = $relationsResolver;
    }

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @return array<RelationsResolvableActivityInterface>
     */
    public function process(
        array $activities,
        string $feedType,
        string|int $userId,
        int $limit,
        DateTimeImmutable $offsetDate,
        array $queryFilters
    ): array {
        $allUnresolvedReferencesByActivityId = $this->collectAllReferencesByActivityId($activities);
        krsort($this->relationsResolvers);
        
        $resolvedActivities = [];
        $remainingReferences = $this->flattenReferences($allUnresolvedReferencesByActivityId);
        foreach ($this->relationsResolvers as $relationsResolver) {
            $resolvedCollection = $relationsResolver->resolveRelations($remainingReferences);

            $resolvedActivities = $this->applyResolvedCollection(
                $activities, $resolvedCollection, $allUnresolvedReferencesByActivityId
            );
    
            $remainingReferences = $resolvedCollection->getUnsupportedReferences();
            if (empty($remainingReferences)) {
                break;
            }
        }

        if (count($remainingReferences) > 0) {
            // TODO: Handle remaining references missing a supporting resolver?
            // Maybe the developers using our libray *want* to have some references unresolved?
            // If not, how will they be hinted to that missing resolver/resolving?
        }

        return $resolvedActivities;
    }

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @return array<string|int, array<RelationReference>>
     */
    private function collectAllReferencesByActivityId(array $activities): array
    {
        $referencesByActivityId = [];
        foreach ($activities as $activity) {
            $referencesByActivityId[$activity->getId()]['actor'] = new RelationReference(
                RelationReference::ACTOR_OBJECT_TYPE_CONFIG,
                $activity->getActorId()
            );
            $referencesByActivityId[$activity->getId()]['content'] = new RelationReference(
                $activity->getContentType(),
                $activity->getContentId()
            );
            if ($activity->getTargetId() !== null) {
                $referencesByActivityId[$activity->getId()]['target'] = new RelationReference(
                    $activity->getTargetType(),
                    $activity->getTargetId()
                );
            }
        }
        return $referencesByActivityId;
    }

    /**
     * Flattens the references by activity ID into a unique set of references.
     * Uses the reference hash as key to ensure uniqueness.
     * 
     * @param array<string|int, array<string, RelationReference>> $referencesByActivityId
     * @return array<RelationReference>
     */
    private function flattenReferences(array $referencesByActivityId): array
    {
        $uniqueReferences = [];
        foreach ($referencesByActivityId as $activityId => $activityReferences) {
            foreach ($activityReferences as $relationType => $reference) {
                // Use hash as key to automatically handle uniqueness
                $uniqueReferences[$reference->getHashedKey()] = $reference;
            }
        }
        
        return array_values($uniqueReferences);
    }

    private function applyResolvedCollection(
        array $activities, 
        ResolvedReferencesCollection $resolvedCollection, 
        array &$allUnresolvedReferencesByActivityId
    ): array
    {
        $resolved = $resolvedCollection->getResolvedReferences();
        $unresolved = $resolvedCollection->getUnresolvedReferences();
        
        $resolvedActivities = [];
        foreach ($activities as $activity) {
            $actorRef = $allUnresolvedReferencesByActivityId[$activity->getId()]['actor'];
            if (isset($resolved[$actorRef->getType()][$actorRef->getId()]) ) {
                $activity->setResolvedActorOnce($resolved[$actorRef->getType()][$actorRef->getId()]);                
                unset($allUnresolvedReferencesByActivityId[$activity->getId()]['actor']);
            } elseif (isset($unresolved[$actorRef->getHashedKey()])) {
                continue;
            }

            $contentRef = $allUnresolvedReferencesByActivityId[$activity->getId()]['content'];
            if (isset($resolved[$contentRef->getType()][$contentRef->getId()])) {
                $activity->setResolvedContentOnce($resolved[$contentRef->getType()][$contentRef->getId()]);
                unset($allUnresolvedReferencesByActivityId[$activity->getId()]['content']);
            } elseif (isset($unresolved[$contentRef->getHashedKey()])) {
                continue;
            }

            if ($activity->getTargetId() !== null) {
                $targetRef = $allUnresolvedReferencesByActivityId[$activity->getId()]['target'];
                if (isset($resolved[$targetRef->getType()][$targetRef->getId()])) {
                    $activity->setResolvedTargetOnce($resolved[$targetRef->getType()][$targetRef->getId()]);
                    unset($allUnresolvedReferencesByActivityId[$activity->getId()]['target']);
                } elseif (isset($unresolved[$targetRef->getHashedKey()])) {
                    continue;
                }
            }

            $resolvedActivities[] = $activity;
        }
        return $resolvedActivities;
    }
}
