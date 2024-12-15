<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

use DateTimeImmutable;

interface BaseActivityInterface
{
    public function getId(): string|int;
    public function getAction(): string;
    public function getActorId(): string;
    public function getObjectId(): string;
    public function getObjectType(): string;
    public function getTargetId(): ?string;
    public function getTargetType(): ?string;
    public function getMetadata(): array;
    public function getCreatedAt(): DateTimeImmutable;
} 