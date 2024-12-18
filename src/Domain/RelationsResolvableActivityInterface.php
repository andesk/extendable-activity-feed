<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain;

interface RelationsResolvableActivityInterface extends BaseActivityInterface
{
    public function addResolvingPayload(string $key, mixed $payload): void;

    public function setResolvedActorOnce(object|array|false $resolvedActor): void;
    public function setResolvedContentOnce(object|array|false $resolvedObject): void;
    public function setResolvedTargetOnce(object|array|null|false $resolvedTarget): void; 

    public function hasActorResolved(): object|array|false  ;
    public function hasContentResolved(): object|array|false;
    public function hasTargetResolved(): object|array|null|false;

    public function getResolvedActor(): object|array|false  ;
    public function getResolvedContent(): object|array|false;
    public function getResolvedTarget(): object|array|null|false;
} 