<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Persistence;

use Andesk\EAF\Domain\BaseActivityInterface;
use Andesk\EAF\Domain\Repositories\ActivityRepositoryInterface;
use Andesk\EAF\Domain\Persistence\Hooks\ActivitySaveHookInterface;
use Andesk\EAF\Domain\Persistence\Hooks\ActivityDeletionHookInterface;

final class ActivityPersister
{
    private array $prePersistHandlers = [];
    private array $postPersistHandlers = [];

    private array $preDeleteHandlers = [];
    private array $postDeleteHandlers = [];

    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
    ) {}

    public function addPrePersistHandler(ActivitySaveHookInterface $hook): void
    {
        $this->prePersistHandlers[] = $hook;
    }

    public function addPostPersistHandler(ActivitySaveHookInterface $hook): void
    {
        $this->postPersistHandlers[] = $hook;
    }

    public function addPreDeleteHandler(ActivityDeletionHookInterface $hook): void
    {
        $this->preDeleteHandlers[] = $hook;
    }

    public function addPostDeleteHandler(ActivityDeletionHookInterface $hook): void
    {
        $this->postDeleteHandlers[] = $hook;
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