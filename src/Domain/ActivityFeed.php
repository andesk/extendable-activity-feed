<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

class ActivityFeed implements ActivityFeedInterface
{
    private array $activities = [];

    private function __construct(array $activities) {
        foreach ($activities as $activity) {
            if (!$activity instanceof BaseActivityInterface) {
                throw new \InvalidArgumentException('All activities must implement BaseActivityInterface');
            }
            $this->activities[] = $activity;
        }
    }

    /**
     * @param array<BaseActivityInterface> $activities
     */ 
    public static function create(array $activities): self {
        return new self($activities);
    }

    public function getNextOffsetDate(): ?\DateTimeImmutable
    {
        if (empty($this->activities)) {
            return null;
        }

        return end($this->activities)->getCreatedAt();
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->activities);
    }

    public function count(): int
    {
        return count($this->activities);
    }
}