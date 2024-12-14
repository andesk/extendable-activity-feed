<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Fetching;

use DateTimeImmutable;
use Andesk\EAF\Domain\Repositories\ActivityRepositoryInterface;

final class ActivityFetcher implements ActivityFetcherInterface
{
    /** @var QueryFilterProviderInterface[] */
    private array $queryFilterProviders = [];

    /** @var PostProcessorInterface[] */
    private array $postProcessors = [];

    /**
     * @param \Andesk\EAF\Domain\Repositories\ActivityRepositoryInterface $activityRepository
     * @param int $maxPostLoadFillingIterations The max amount of post load iterations to perform filling gaps due to post processing filtering (like permissions or similar). Set to 0 if you do not want to post load at all.
     */
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly int $maxPostLoadFillingIterations = 3,
    ) {}

    public function addQueryFilterProvider(QueryFilterProviderInterface $provider, $priority = 500): void
    {
        if (isset($this->queryFilterProviders[$priority])) {
            throw new \InvalidArgumentException('Processor already registered for priority ' . $priority);
        }
        $this->queryFilterProviders[$priority] = $provider;
    }

    public function addPostProcessor(PostProcessorInterface $processor, $priority = 500): void
    {
        if (isset($this->postProcessors[$priority])) {
            throw new \InvalidArgumentException('Processor already registered for priority ' . $priority);
        }
        $this->postProcessors[$priority] = $processor;
    }   

    public function getActivities(
        string $feedType,
        string|int $userId, 
        int $limit = 20, 
        DateTimeImmutable $offsetDate = null,
        array $queryFilters = [],
        int $postLoadIteration = 0
    ): array {
        $queryFilters = $this->expandQueryFilters($queryFilters, $feedType, $userId, $limit, $offsetDate);

        $activities = $this->activityRepository->getActivities(
            $feedType,
            $userId,
            $limit,
            $offsetDate,
            $queryFilters
        );

        $filteredAndExpandedActivities = $this->postProcessActivities($activities, $feedType, $userId, $limit, $offsetDate, $queryFilters);

        if ($this->needsPostLoadFilling($filteredAndExpandedActivities, $activities, $limit)) {
            $filteredAndExpandedActivities = $this->postLoadActivitiesFilling($filteredAndExpandedActivities, $activities, $feedType, $userId, $limit, $offsetDate, $queryFilters, $postLoadIteration);
        }

        return $filteredAndExpandedActivities;
    }

    private function expandQueryFilters(array $queryFilters, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate): array
    {
        foreach($this->queryFilterProviders as $provider) {
            $queryFilters = $provider->expand($queryFilters, $feedType, $userId, $limit, $offsetDate);
        }

        return $queryFilters;
    }

    private function postProcessActivities(array $activities, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate, array $queryFilters): array
    {
        foreach($this->postProcessors as $processor) {
            $filteredActivities = $processor->process($activities,$feedType, $userId, $limit, $offsetDate, $queryFilters);
        }

        return $filteredActivities;
    }

    private function needsPostLoadFilling(array $filteredActivities, array $allFetchedActivities, int $limit): bool
    {
        return count($filteredActivities) < $limit && count($allFetchedActivities) !== count($filteredActivities);
    }

    private function postLoadActivitiesFilling(array $filteredActivities, array $allFetchedActivities, string $feedType, string|int $userId, int $limit, DateTimeImmutable $offsetDate, array $queryFilters, int $postLoadIteration): array
    {
        if ($postLoadIteration >= $this->maxPostLoadFillingIterations) {
            return $filteredActivities;
        }

        if (count($allFetchedActivities) === 0) {
            return $allFetchedActivities;
        }

        $lackingActivitiesCount = $limit - count($filteredActivities);
        $postLoadLimit = $this->getNextPostLoadLimit($lackingActivitiesCount);
        
        $latestActivity = end($allFetchedActivities);
        $newOffsetDate = $latestActivity->getCreatedAt();
        $filteredActivities = array_merge(
            $filteredActivities,
            array_slice(
                $this->getActivities(
                    $feedType, $userId, $postLoadLimit, $newOffsetDate, $queryFilters, $postLoadIteration + 1  
                ), 
                0, 
                $lackingActivitiesCount
            )
        );
        
        return $filteredActivities;
    }

    /**
     * TODO: very naive, make this configurable or introduce strategy pattern even? Later!
     * 
     * @param int $lackingActivitiesCount The amount of activities that are missing from the filtered activities array.
     * @return int The amount of activities to fetch from the database.
     */
    private function getNextPostLoadLimit(int $lackingActivitiesCount): int
    {
        $newPostLoadLimit = $lackingActivitiesCount * 2; // let's fetch twice as much as we need, to be safe

        if ($newPostLoadLimit < 8) {
            $newPostLoadLimit = 8; // let's fetch at least 8 activities, to be safe
        }

        return $newPostLoadLimit;
    }
} 