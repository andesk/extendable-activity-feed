<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching\QueryFilters;

use Andesk\EAF\Domain\Fetching\QueryFilterProviderInterface;
use Andesk\EAF\Domain\Fetching\ActivityFetcherInterface;
use DateTimeImmutable;

class CoreFiltersFilterProvider implements QueryFilterProviderInterface
{
    public function expand(array $queryFilters, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate): array
    {
        switch($feedType) {
            case ActivityFetcherInterface::FEED_TYPE_ALL:
                break;
            case ActivityFetcherInterface::FEED_TYPE_ACTOR:
                $queryFilters[] = ActorIdQueryFilter::createForActor($userId);
                break;
            case ActivityFetcherInterface::FEED_TYPE_SINGLE_USER:
                $queryFilters[] = UserIdsQueryFilter::createForSingleUser($userId);
                break;
            case ActivityFetcherInterface::FEED_TYPE_USER_PEERS:
                $queryFilters[] = UserPeersQueryFilter::createForUser($userId);
                break;
            case ActivityFetcherInterface::FEED_TYPE_USER_GROUPS:
                $queryFilters[] = UserNetworkQueryFilter::createForUser($userId);
                break;
        }
        
        return $queryFilters;
    }
}
