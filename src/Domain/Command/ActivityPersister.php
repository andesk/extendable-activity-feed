<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Command;

use Andesk\EAF\Domain\BaseActivityInterface;
use Andesk\EAF\Domain\Repositories\ActivityRepositoryInterface;
use Andesk\EAF\Domain\Command\Hooks\ActivitySaveHookInterface;
use Andesk\EAF\Domain\Command\Hooks\ActivityDeletionHookInterface;

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

        $id = $this->activityRepository->save($activity);

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

        $this->activityRepository->delete($activityId);

        foreach ($this->postDeleteHandlers as $handler) {
            $handler->handle($activityId);
        }
    }
} 