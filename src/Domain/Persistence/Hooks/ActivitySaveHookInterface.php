<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Persistence\Hooks;

use Andesk\EAF\Domain\BaseActivityInterface;

interface ActivitySaveHookInterface
{
    public function handle(BaseActivityInterface $activity): void;
}