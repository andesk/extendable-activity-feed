<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching\RelationResolver;

use Andesk\EAF\Domain\RelationsResolvableActivityInterface;

interface ActivityRelationsResolverInterface
{
    public function supportsResolveSingleActivity(RelationsResolvableActivityInterface $activity): bool;

    public function resolveSingleActivity(RelationsResolvableActivityInterface $activity): bool;

    public function supportsResolveActivitiesPre(array $activities): bool;

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @return array<RelationsResolvableActivityInterface>
     */
    public function resolveActivitiesPre(array $activities): array;

    public function supportsResolveActivitiesPost(array $activities): bool;

    /**
     * @param array<RelationsResolvableActivityInterface> $activities
     * @return array<RelationsResolvableActivityInterface>
     */
    public function resolveActivitiesPost(array $activities): array;
}