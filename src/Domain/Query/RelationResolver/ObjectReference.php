<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

/**
 * Value object representing a reference to a resolvable object.
 * Provides efficient hashing for lookups and caching.
 */
final class ObjectReference
{
    private readonly string $hash;
    
    public function __construct(
        public readonly string $type,
        public readonly string $id
    ) {
        $this->hash = self::generateHash($type, $id);
    }
    
    public function hash(): string
    {
        return $this->hash;
    }
    
    public static function generateHash(string $type, string $id): string
    {
        return sprintf('%s:%s', $type, $id);
    }
} 