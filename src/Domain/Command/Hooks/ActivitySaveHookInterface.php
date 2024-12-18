<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Command\Hooks;

use Andesk\EAF\Domain\BaseActivityInterface;

interface ActivitySaveHookInterface
{
    public function handle(BaseActivityInterface $activity): void;
}