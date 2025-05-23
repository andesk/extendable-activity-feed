<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query;

use Andesk\EAF\Domain\ActivityFeedInterface;
use DateTimeImmutable;

interface ActivityFetcherInterface
{
    const FEED_HOOKS_TYPE_ALL = 'all';
    const FEED_TYPE_ALL = 'all';
    const FEED_TYPE_ACTOR = 'actor';
    const FEED_TYPE_SINGLE_USER = 'user';
    const FEED_TYPE_USER_PEERS = 'peers';
    const FEED_TYPE_USER_GROUPS = 'groups';
    
    public function getActivityFeed (
        string $feedType,
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null,
        array $queryFilters = []
    ): ActivityFeedInterface;
}
