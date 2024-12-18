<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\PostProcessors;

use Andesk\EAF\Domain\Query\RelationResolver\ActivityRelationsResolverInterface;
use Andesk\EAF\Domain\Query\PostProcessorInterface;
use Andesk\EAF\Domain\Query\RelationResolver\GracefulNonResolver;
use Andesk\EAF\Domain\RelationsResolvableActivityInterface;
use DateTimeImmutable;

class RelationsResolverPostProcessor implements PostProcessorInterface
{
    public function __construct(
        readonly private ?ActivityRelationsResolverInterface $actorResolver,
        readonly private ?ActivityRelationsResolverInterface $contentResolver,
        readonly private ?ActivityRelationsResolverInterface $targetResolver,
    ) {
        if ($actorResolver === null) {
            $actorResolver = new GracefulNonResolver();
        }
        $this->actorResolver = $actorResolver;

        if ($contentResolver === null) {
            $contentResolver = new GracefulNonResolver();
        }
        $this->contentResolver = $contentResolver;

        if ($targetResolver === null) {
            $targetResolver = new GracefulNonResolver();
        }
        $this->targetResolver = $targetResolver;
    }

    public function process(array $activities, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate, array $queryFilters): array
    {        
        $activities = $this->resolveActivitiesPre($activities);
        
        foreach ($activities as $key => $activity) {
            $resolvedActivity = $this->resolveSingleActivity($activity);
            if (!$resolvedActivity) {
                unset($activities[$key]);
            }
        }
        
        return $this->resolveActivitiesPost(array_values($activities));
    }

    private function resolveActivitiesPre(array $activities): array
    {
        if ($this->actorResolver->supportsResolveActivitiesPre($activities)) {
            return $this->actorResolver->resolveActivitiesPre($activities);
        }

        return $activities;
    }

    private function resolveSingleActivity(RelationsResolvableActivityInterface $activity): bool
    {
        return $this->actorResolver->resolveSingleActivity($activity) &&
            $this->targetResolver->resolveSingleActivity($activity) &&
            $this->contentResolver->resolveSingleActivity($activity);
    }

    private function resolveActivitiesPost(array $activities): array
    {
        if ($this->actorResolver->supportsResolveActivitiesPost($activities)) {
            return $this->actorResolver->resolveActivitiesPost($activities);
        }

        return $activities;
    }
}
