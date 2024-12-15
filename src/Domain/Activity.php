<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

use DateTimeImmutable;
use Andesk\EAF\Domain\Exceptions\DoubleResolveException;

class Activity implements BaseActivityInterface, RelationsResolvableActivityInterface
{
    private array $resolvingPayload = [];
    private object|array|null|false $resolvedActor = null;
    private object|array|null|false $resolvedContent = null;
    private object|array|null|false $resolvedTarget = null;  

    private function __construct(
        private readonly string|int|null $id,
        private readonly string $action,
        private readonly string $actorId,
        private readonly string $contentId,
        private readonly string $contentType,
        private readonly ?string $targetId = null,
        private readonly ?string $targetType = null,
        private readonly array $additionalData = [],
        private readonly ?DateTimeImmutable $createdAt = null
    ) {}

    public static function create(
        string $action,
        string $actorId,
        string $contentId,
        string $contentType,
        ?string $targetId = null,
        ?string $targetType = null,
        array $additionalData = [],
        ?DateTimeImmutable $createdAt = null
    ): self
    {
        return new self(null, $action, $actorId, $contentId, $contentType, $targetId, $targetType, $additionalData, $createdAt);
    }  

    public static function createWithId(
        string|int $id,
        string $action,
        string $actorId,
        string $contentId,
        string $contentType,
        ?string $targetId = null,
        ?string $targetType = null,
        array $additionalData = [],
        ?DateTimeImmutable $createdAt = null
    ): self
    {
        return new self($id, $action, $actorId, $contentId, $contentType, $targetId, $targetType, $additionalData, $createdAt);
    }  

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getActorId(): string
    {
        return $this->actorId;
    }

    public function getContentId(): string
    {
        return $this->contentId;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt ?? new DateTimeImmutable();
    }

    public function addResolvingPayload(string $key, mixed $payload): void
    {
        $this->resolvingPayload[$key] = $payload;
    }

    public function setResolvedActorOnce(object|array|false $resolvedActor): void
    {
        if ($this->resolvedActor !== null) {
            throw new DoubleResolveException();
        }

        $this->resolvedActor = $resolvedActor;
    }

    public function setResolvedContentOnce(object|array|false $resolvedContent): void
    {
        if ($this->resolvedContent !== null) {
            throw new DoubleResolveException();
        }

        $this->resolvedContent = $resolvedContent;
    }

    public function setResolvedTargetOnce(object|array|null|false $resolvedTarget): void
    {
        if ($this->resolvedTarget !== null) {
            throw new DoubleResolveException();
        }

        $this->resolvedTarget = $resolvedTarget;
    }

    public function hasActorResolved(): object|array|false
    {
        return $this->resolvedActor !== null;
    }

    public function hasContentResolved(): object|array|false
    {
        return $this->resolvedContent !== null;
    }

    public function hasTargetResolved(): object|array|null|false
    {
        return $this->resolvedTarget !== null;
    }

    public function getResolvedActor(): object|array|false
    {
        return $this->resolvedActor;
    }

    public function getResolvedContent(): object|array|false
    {
        return $this->resolvedContent;
    }

    public function getResolvedTarget(): object|array|null|false
    {
        return $this->resolvedTarget;
    }
} 