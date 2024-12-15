<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching\RelationResolver;

use Andesk\EAF\Domain\RelationsResolvableActivityInterface;

class FilteringNonResolver implements ActivityRelationsResolverInterface
{
    public function supportsResolveSingleActivity(RelationsResolvableActivityInterface $activity): bool
    {
        return false;
    }

    public function resolveSingleActivity(RelationsResolvableActivityInterface $activity, bool $defaultToHappy = true): bool
    {
        $activity->setResolvedActorOnce(false);
        $activity->setResolvedContentOnce(false);
        $activity->setResolvedTargetOnce(false);
        
        return false;
    }

    public function supportsResolveActivitiesPre(array $activities): bool
    {
        return false;
    }

    public function resolveActivitiesPre(array $activities): array
    {
        return [];
    }

    public function supportsResolveActivitiesPost(array $activities): bool
    {
        return false;
    }

    public function resolveActivitiesPost(array $activities): array
    {
        return [];
    }
}