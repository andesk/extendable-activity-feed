<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Query\RelationResolver;

use InvalidArgumentException;

/**
 * Value object representing the type of relation to resolve.
 */
final class RelationType
{
    private const VALID_TYPES = ['actor', 'content', 'target'];

    /**
     * Maps relation types to their unique keys for efficient lookups.
     */
    private const TYPE_KEYS = [
        'actor' => 'act',
        'content' => 'cnt',
        'target' => 'tgt',
    ];

    private function __construct(
        private readonly string $type
    ) {
        if (!in_array($type, self::VALID_TYPES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid relation type "%s". Valid types are: %s',
                    $type,
                    implode(', ', self::VALID_TYPES)
                )
            );
        }
    }

    public static function actor(): self
    {
        return new self('actor');
    }

    public static function content(): self
    {
        return new self('content');
    }

    public static function target(): self
    {
        return new self('target');
    }

    public function equals(self $other): bool
    {
        return $this->type === $other->type;
    }

    public function toString(): string
    {
        return $this->type;
    }

    /**
     * Returns a unique, short key for this relation type.
     * This is useful for efficient array lookups and caching.
     */
    public function key(): string
    {
        return self::TYPE_KEYS[$this->type];
    }

    /**
     * Returns all possible relation types.
     * @return array<RelationType>
     */
    public static function all(): array
    {
        return [
            self::actor(),
            self::content(),
            self::target(),
        ];
    }
} 