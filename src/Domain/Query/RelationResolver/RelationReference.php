<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

/**
 * Value object representing a reference to a relation that needs to be resolved.
 * Provides simple hashing for lookups and caching.
 */
final class RelationReference
{
    const ACTOR_OBJECT_TYPE_CONFIG = 'EAFActor';
    private readonly string $hashedKey;
    
    public function __construct(
        private readonly string $type,
        private readonly string|int $id
    ) {
        $this->hashedKey = self::generateHash($type, (string)$id);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getId(): string|int
    {
        return $this->id;
    }
    
    public function getHashedKey(): string
    {
        return $this->hashedKey;
    }
    
    public static function generateHash(string $type, string $id): string
    {
        return sprintf('%s::%s', $type, $id);
    }

    public static function createFromHash(string $hash): self
    {
        [$type, $id] = explode('::', $hash);
        
        return new self($type, $id);
    }

    public function equals(self $other): bool
    {
        return $this->hashedKey === $other->hashedKey;
    }
} 