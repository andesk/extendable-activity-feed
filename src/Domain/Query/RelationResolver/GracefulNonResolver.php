<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

use Andesk\EAF\Domain\RelationsResolvableActivityInterface;

class GracefulNonResolver implements ActivityRelationsResolverInterface
{
    public function supportsResolveSingleActivity(RelationsResolvableActivityInterface $activity): bool
    {
        return true;
    }

    public function resolveSingleActivity(RelationsResolvableActivityInterface $activity, bool $defaultToHappy = true): bool
    {
        $activity->setResolvedActorOnce(false);
        $activity->setResolvedContentOnce(false);
        $activity->setResolvedTargetOnce(false);
        
        return true;
    }

    public function supportsResolveActivitiesPre(array $activities): bool
    {
        return true;
    }

    public function resolveActivitiesPre(array $activities): array
    {
        return $activities;
    }

    public function supportsResolveActivitiesPost(array $activities): bool
    {
        return true;
    }

    public function resolveActivitiesPost(array $activities): array
    {
        return $activities;
    }
}
