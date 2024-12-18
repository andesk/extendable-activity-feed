<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\QueryFilters;

use Andesk\EAF\Domain\Query\QueryFilterInterface;

class ContentTypesFilter implements QueryFilterInterface
{
    private function __construct(private readonly array $contentTypes) {}

    public static function createForSingleContentType(string|int $contentType): self
    {
        return new self([$contentType]);
    }

    /**
     * @param array<string|int> $contentTypes
     */
    public static function createForMultipleContentTypes(array $contentTypes): self
    {
        return new self($contentTypes);
    }

    /**
     * @return array<string>
     */
    public function getContentTypes(): array
    {
        return $this->contentTypes;
    }
}
