<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterInterface;

class SpecificContentsFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $contentRefTuples) {}

    /**
     * @param array{contentId: string, contentType: string} $contentRefTuple
     */
    public static function createForSingleContent(array $contentRefTuple): self
    {
        return new self([$contentRefTuple]);
    }

    /**
     * @param array<array{contentId: string, contentType: string}> $contentRefTuples
     */
    public static function createForMultipleContentTypes(array $contentRefTuples): self
    {
        return new self($contentRefTuples);
    }

    /**
     * @return array<array{contentId: string, contentType: string}>
     */
    public function getContentRefTuples(): array
    {
        return $this->contentRefTuples;
    }
}
