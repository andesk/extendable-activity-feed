<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\ValueObject;

/**
 * Value object that encapsulates the resolution strategy for activity relations.
 * It determines which resolution phases should be executed during the post-processing.
 */
class ResolutionStrategy
{
    public function __construct(
        private readonly bool $preResolve = false,
        private readonly bool $singleResolve = true,
        private readonly bool $postResolve = false,
    ) {}

    /**
     * Creates a default strategy that only performs single resolution.
     * This is the most memory-efficient approach as it resolves one activity at a time.
     */
    public static function createDefault(): self
    {
        return new self(
            preResolve: false,
            singleResolve: true,
            postResolve: false,
        );
    }

    /**
     * Creates a batch-optimized strategy that uses pre and post resolution.
     * This is the most performance-efficient approach for large sets of activities
     * as it allows batch loading of relations.
     */
    public static function createBatchOptimized(): self
    {
        return new self(
            preResolve: true,
            singleResolve: false,
            postResolve: true,
        );
    }

    /**
     * Creates a comprehensive strategy that uses all resolution phases.
     * This provides maximum flexibility but might be overkill for most use cases.
     * Use this when you need both batch optimization and single resolution capabilities.
     */
    public static function createComprehensive(): self
    {
        return new self(
            preResolve: true,
            singleResolve: true,
            postResolve: true,
        );
    }

    public function shouldPreResolve(): bool
    {
        return $this->preResolve;
    }

    public function shouldSingleResolve(): bool
    {
        return $this->singleResolve;
    }

    public function shouldPostResolve(): bool
    {
        return $this->postResolve;
    }
} 