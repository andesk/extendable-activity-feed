<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Persistence\Hooks;

interface ActivityDeletionHookInterface
{
    public function handle(string|int $activityId): void;
}