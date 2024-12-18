<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\PostProcessors;

use Andesk\EAF\Domain\Query\RelationResolver\BatchObjectResolverInterface;
use Andesk\EAF\Domain\Query\RelationResolver\SingleObjectResolverInterface;
use Andesk\EAF\Domain\Query\RelationResolver\ObjectReference;
use Andesk\EAF\Domain\Query\PostProcessorInterface;
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
    public function __construct(
        private readonly ?BatchObjectResolverInterface $preResolver = null,
        private readonly SingleObjectResolverInterface $singleResolver,
        private readonly ?BatchObjectResolverInterface $postResolver = null,
    ) {}

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
        // Pre-batch phase (e.g., for API rate limiting, bulk loading)
        if ($this->preResolver !== null) {
            $preReferences = $this->collectReferences($activities);
            $preResolved = $this->preResolver->resolveBatch($preReferences);
            $this->applyPreResolved($activities, $preResolved);
        }
        
        // Single resolution phase (required)
        foreach ($activities as $key => $activity) {
            if (!$activity instanceof RelationsResolvableActivityInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Activity must implement %s, got %s',
                        RelationsResolvableActivityInterface::class,
                        get_debug_type($activity)
                    )
                );
            }
            
            $references = $this->collectUnresolvedReferences($activity);
            foreach ($references as $ref) {
                $resolved = $this->singleResolver->resolve($ref);
                if (!$this->applySingleResolved($activity, $ref, $resolved)) {
                    unset($activities[$key]);
                    break;
                }
            }
        }
        
        $activities = array_values($activities);
        
        // Post-batch phase (e.g., for optimization, denormalization)
        if ($this->postResolver !== null) {
            $postReferences = $this->collectRemainingReferences($activities);
            $postResolved = $this->postResolver->resolveBatch($postReferences);
            $this->applyPostResolved($activities, $postResolved);
        }

        return $activities;
    }

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @return array<ObjectReference>
     */
    private function collectReferences(array $activities): array
    {
        $references = [];
        foreach ($activities as $activity) {
            $references[] = new ObjectReference('actor', $activity->getActorId());
            $references[] = new ObjectReference('content', $activity->getContentId());
            if ($activity->getTargetId() !== null) {
                $references[] = new ObjectReference('target', $activity->getTargetId());
            }
        }
        return array_unique($references, SORT_REGULAR);
    }

    /**
     * @return array<ObjectReference>
     */
    private function collectUnresolvedReferences(RelationsResolvableActivityInterface $activity): array
    {
        $references = [];
        if (!$activity->hasActorResolved()) {
            $references[] = new ObjectReference('actor', $activity->getActorId());
        }
        if (!$activity->hasContentResolved()) {
            $references[] = new ObjectReference('content', $activity->getContentId());
        }
        if (!$activity->hasTargetResolved() && $activity->getTargetId() !== null) {
            $references[] = new ObjectReference('target', $activity->getTargetId());
        }
        return $references;
    }

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @return array<ObjectReference>
     */
    private function collectRemainingReferences(array $activities): array
    {
        $references = [];
        foreach ($activities as $activity) {
            foreach ($this->collectUnresolvedReferences($activity) as $ref) {
                $references[] = $ref;
            }
        }
        return array_unique($references, SORT_REGULAR);
    }

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @param array<string, object> $resolved Resolved objects indexed by their reference hash
     */
    private function applyPreResolved(array $activities, array $resolved): void
    {
        foreach ($activities as $activity) {
            // Only try to resolve what's actually available
            $actorRefHash = ObjectReference::generateHash('actor', $activity->getActorId());
            if (isset($resolved[$actorRefHash])) {
                $activity->setResolvedActorOnce($resolved[$actorRefHash()]);
            }

            $contentRefHash = ObjectReference::generateHash('content', $activity->getContentId());
            if (isset($resolved[$contentRefHash])) {
                $activity->setResolvedContentOnce($resolved[$contentRefHash]);
            }

            if ($activity->getTargetId() !== null) {
                $targetRefHash = ObjectReference::generateHash('target', $activity->getTargetId());
                if (isset($resolved[$targetRefHash])) {
                    $activity->setResolvedTargetOnce($resolved[$targetRefHash]);
                }
            }
        }
    }

    private function applySingleResolved(
        RelationsResolvableActivityInterface $activity,
        ObjectReference $ref,
        ?object $resolved
    ): bool {
        if ($resolved === null) {
            return false;
        }

        match ($ref->type) {
            'actor' => $activity->setResolvedActorOnce($resolved),
            'content' => $activity->setResolvedContentOnce($resolved),
            'target' => $activity->setResolvedTargetOnce($resolved),
            default => throw new InvalidArgumentException("Unknown reference type: {$ref->type}")
        };

        return true;
    }

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @param array<string, object> $resolved Resolved objects indexed by their reference hash
     */
    private function applyPostResolved(array $activities, array $resolved): void
    {
        foreach ($activities as $activity) {
            // Only try to resolve remaining unresolved relations
            if (!$activity->hasActorResolved()) {
                $actorRefHash = ObjectReference::generateHash('actor', $activity->getActorId());
                if (isset($resolved[$actorRefHash])) {
                    $activity->setResolvedActorOnce($resolved[$actorRefHash]);
                }
            }

            if (!$activity->hasContentResolved()) {
                $contentRefHash = ObjectReference::generateHash('content', $activity->getContentId());
                if (isset($resolved[$contentRefHash])) {
                    $activity->setResolvedContentOnce($resolved[$contentRefHash]);
                }
            }

            if (!$activity->hasTargetResolved() && $activity->getTargetId() !== null) {
                $targetRefHash = ObjectReference::generateHash('target', $activity->getTargetId());
                if (isset($resolved[$targetRefHash])) {
                    $activity->setResolvedTargetOnce($resolved[$targetRefHash]);
                }
            }
        }
    }
}
