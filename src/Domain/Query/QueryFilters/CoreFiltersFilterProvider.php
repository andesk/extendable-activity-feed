<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterProviderInterface;
use Andesk\EAF\Domain\Query\ActivityFetcherInterface;
use DateTimeImmutable;

class CoreFiltersFilterProvider implements QueryFilterProviderInterface
{
    public function expand(array $queryFilters, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate): array
    {
        switch($feedType) {
            case ActivityFetcherInterface::FEED_TYPE_ALL:
                break;
            case ActivityFetcherInterface::FEED_TYPE_ACTOR:
                $queryFilters[] = ActorIdFilter::createForActor($userId);
                break;
            case ActivityFetcherInterface::FEED_TYPE_SINGLE_USER:
                $queryFilters[] = UserIdsFilter::createForSingleUser($userId);
                break;
            case ActivityFetcherInterface::FEED_TYPE_USER_PEERS:
                $queryFilters[] = UserPeersFilter::createForUser($userId);
                break;
            case ActivityFetcherInterface::FEED_TYPE_USER_GROUPS:
                $queryFilters[] = UserNetworkFilter::createForUser($userId);
                break;
        }
        
        return $queryFilters;
    }
}
