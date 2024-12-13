<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\PersistenceHandlers;

use Andesk\EAF\Domain\BaseActivityInterface;

interface ActivityPersistenceHandlerInterface
{
    public function handle(BaseActivityInterface $activity): void;
}