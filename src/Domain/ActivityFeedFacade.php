<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

use Andesk\EAF\Domain\Services\ActivityPersister;
use Andesk\EAF\Domain\Services\ActivityFetcher;
use Andesk\EAF\Domain\BaseActivityInterface;

final class ActivityFeedFacade
{
    public function __construct(
        private readonly ActivityPersister $activityPersister,
        private readonly ActivityFetcher $activityFetcher,
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
} 