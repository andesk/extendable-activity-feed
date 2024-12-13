<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

interface RelationsResolvableActivityInterface
{
    public function setResolvedActorOnce(object|array $resolvedActor): void;
    public function setResolvedObjectOnce(object|array $resolvedObject): void;
    public function setResolvedTargetOnce(object|array|null $resolvedTarget): void; 

    public function getResolvedActor(): object|array;
    public function getResolvedObject(): object|array;
    public function getResolvedTarget(): object|array|null;
} 