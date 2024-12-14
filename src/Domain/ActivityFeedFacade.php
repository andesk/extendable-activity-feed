<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

use Andesk\EAF\Domain\Persistence\ActivityPersister;
use Andesk\EAF\Domain\Fetching\ActivityFetcherInterface;
use Andesk\EAF\Domain\BaseActivityInterface;
use DateTimeImmutable;
final class ActivityFeedFacade
{
    public function __construct(
        private readonly ActivityPersister $activityPersister,
        private readonly ActivityFetcherInterface $activityFetcher,
    ) {}

    /**
     * @return string|int The id  of the stored activity
     */
    public function persistNewActivity(BaseActivityInterface $activity, $flushToDB = true): string|int
    {
        return $this->activityPersister->persist($activity, $flushToDB);
    }

    public function deleteActivityById(string|int $activityId, $flushToDB = true): void
    {
        $this->activityPersister->deleteById($activityId, $flushToDB);
    }

    public function getFeed(
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array {
        return $this->activityFetcher->getActivities(ActivityFetcherInterface::FEED_TYPE_ALL, '',$limit, $offsetDate);
    }

    public function getFeedForActor(
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array {
        return $this->activityFetcher->getActivities(ActivityFetcherInterface::FEED_TYPE_ACTOR, $userId, $limit, $offsetDate);
    }

    public function getFeedFromUser(
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array {
        return $this->activityFetcher->getActivities(ActivityFetcherInterface::FEED_TYPE_SINGLE_USER, $userId, $limit, $offsetDate);
    }

    public function getFeedFromUserPeers(
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array {
        return $this->activityFetcher->getActivities(ActivityFetcherInterface::FEED_TYPE_USER_PEERS, $userId, $limit, $offsetDate);
    }

    public function getFeedFromUserNetwork(
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null
    ): array {
        return $this->activityFetcher->getActivities(ActivityFetcherInterface::FEED_TYPE_USER_GROUPS, $userId, $limit, $offsetDate);
    }

} 