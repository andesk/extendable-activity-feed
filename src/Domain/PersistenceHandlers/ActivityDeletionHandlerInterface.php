<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\PersistenceHandlers;

interface ActivityDeletionHandlerInterface
{
    public function handle(string|int $activityId): void;
}