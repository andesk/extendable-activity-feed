<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Services;

use Andesk\EAF\Domain\BaseActivityInterface;
use Andesk\EAF\Domain\Repositories\ActivityRepositoryInterface;
use Andesk\EAF\Domain\PersistenceHandlers\ActivityPersistenceHandlerInterface;
use Andesk\EAF\Domain\PersistenceHandlers\ActivityDeletionHandlerInterface;

final class ActivityPersister
{
    private array $prePersistHandlers = [];
    private array $postPersistHandlers = [];

    private array $preDeleteHandlers = [];
    private array $postDeleteHandlers = [];

    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
    ) {}


    public function addPrePersistHandler(ActivityPersistenceHandlerInterface $handler): void
    {
        $this->prePersistHandlers[] = $handler;
    }

    public function addPostPersistHandler(ActivityPersistenceHandlerInterface $handler): void
    {
        $this->postPersistHandlers[] = $handler;
    }

    public function addPreDeleteHandler(ActivityDeletionHandlerInterface $handler): void
    {
        $this->preDeleteHandlers[] = $handler;
    }

    public function addPostDeleteHandler(ActivityDeletionHandlerInterface $handler): void
    {
        $this->postDeleteHandlers[] = $handler;
    }

    /**
     * @return string|int The id  of the stored activity
     */
    public function persist(BaseActivityInterface $activity, $flushToDB = true): string|int
    {
        foreach ($this->prePersistHandlers as $handler) {
            $handler->handle($activity);
        }

        $id = $this->activityRepository->persist($activity, $flushToDB);

        foreach ($this->postPersistHandlers as $handler) {
            $handler->handle($activity);
        }

        return $id;
    }

    public function deleteById(string|int $activityId, $flushToDB = true): void
    {
        foreach ($this->preDeleteHandlers as $handler) {
            $handler->handle($activityId);
        }

        $this->activityRepository->delete($activityId, $flushToDB);

        foreach ($this->postDeleteHandlers as $handler) {
            $handler->handle($activityId);
        }
    }
} 