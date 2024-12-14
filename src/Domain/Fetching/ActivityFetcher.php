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

    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
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
        array $queryFilters = []
    ): array {
        $queryFilters = $this->expandQueryFilters($queryFilters, $feedType, $userId, $limit, $offsetDate);

        $activities = $this->activityRepository->getActivities(
            $feedType,
            $userId,
            $limit,
            $offsetDate,
            $queryFilters
        );

        return $this->postProcessActivities($activities, $feedType, $userId, $limit, $offsetDate, $queryFilters);
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
            $activities = $processor->process($activities, $feedType, $userId, $limit, $offsetDate, $queryFilters);
        }

        return $activities;
    }

} 