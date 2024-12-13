<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

use DateTimeImmutable;
use Andesk\EAF\Domain\Exceptions\DoubleResolveException;

class Activity implements BaseActivityInterface, RelationsResolvableActivityInterface
{
    private object|array $resolvedActor;
    private object|array $resolvedObject;
    private object|array|null $resolvedTarget;  

    private function __construct(
        private readonly string|int|null $id,
        private readonly string $type,
        private readonly string $actorId,
        private readonly string $objectId,
        private readonly string $objectType,
        private readonly ?string $targetId = null,
        private readonly ?string $targetType = null,
        private readonly array $metadata = [],
        private readonly ?DateTimeImmutable $createdAt = null
    ) {}

    public static function create(
        string $type,
        string $actorId,
        string $objectId,
        string $objectType,
        ?string $targetId = null,
        ?string $targetType = null,
        array $metadata = [],
        ?DateTimeImmutable $createdAt = null
    ): self
    {
        return new self(null, $type, $actorId, $objectId, $objectType, $targetId, $targetType, $metadata, $createdAt);
    }  

    public static function createWithId(
        string|int $id,
        string $type,
        string $actorId,
        string $objectId,
        string $objectType,
        ?string $targetId = null,
        ?string $targetType = null,
        array $metadata = [],
        ?DateTimeImmutable $createdAt = null
    ): self
    {
        return new self($id, $type, $actorId, $objectId, $objectType, $targetId, $targetType, $metadata, $createdAt);
    }  

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getActorId(): string
    {
        return $this->actorId;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt ?? new DateTimeImmutable();
    }

    public function setResolvedActorOnce(object|array $resolvedActor): void
    {
        if ($this->resolvedActor !== null) {
            throw new DoubleResolveException();
        }

        $this->resolvedActor = $resolvedActor;
    }

    public function setResolvedObjectOnce(object|array $resolvedObject): void
    {
        if ($this->resolvedObject !== null) {
            throw new DoubleResolveException();
        }

        $this->resolvedObject = $resolvedObject;
    }

    public function setResolvedTargetOnce(object|array|null $resolvedTarget): void
    {
        if ($this->resolvedTarget !== null) {
            throw new DoubleResolveException();
        }

        $this->resolvedTarget = $resolvedTarget;
    }

    public function getResolvedActor(): object|array
    {
        return $this->resolvedActor;
    }

    public function getResolvedObject(): object|array
    {
        return $this->resolvedObject;
    }

    public function getResolvedTarget(): object|array|null
    {
        return $this->resolvedTarget;
    }

} 