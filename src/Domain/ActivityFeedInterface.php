<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

interface ActivityFeedInterface extends \IteratorAggregate, \Countable
{
    public function getNextOffsetDate(): ?\DateTimeImmutable;
}