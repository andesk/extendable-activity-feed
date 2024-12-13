<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Services;

use Andesk\EAF\Domain\Repositories\ActivityRepositoryInterface;
use Andesk\EAF\Domain\PersistenceHandlers\ActivityPersistenceHandlerInterface;
use Andesk\EAF\Domain\PersistenceHandlers\ActivityDeletionHandlerInterface;
use DateTimeImmutable;

final class ActivityFetcher
{
    private array $preFetchHandlers = [];
    private array $postFetchHandlers = [];
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
    ) {}

    public function addPrePersistHandler(ActivityPersistenceHandlerInterface $handler): void
    {
        $this->preFetchHandlers[] = $handler;
    }

    public function addPostPersistHandler(ActivityPersistenceHandlerInterface $handler): void
    {
        $this->postFetchHandlers[] = $handler;
    }


} 