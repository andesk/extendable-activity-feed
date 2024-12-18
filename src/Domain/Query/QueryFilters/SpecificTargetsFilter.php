<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterInterface;

class SpecificTargetsFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $targetRefTuples) {}

    /**
     * @param array{targetId: string, targetType: string} $targetRefTuple
     */
    public static function createForSingleTarget(array $targetRefTuple): self
    {
        return new self([$targetRefTuple]);
    }

    /**
     * @param array<array{targetId: string, targetType: string}> $targetRefTuples
     */
    public static function createForMultipleTargets(array $targetRefTuples): self
    {
        return new self($targetRefTuples);
    }

    /**
     * @return array<array{targetId: string, targetType: string}>
     */
    public function getTargetRefTuples(): array
    {
        return $this->targetRefTuples;
    }
}
