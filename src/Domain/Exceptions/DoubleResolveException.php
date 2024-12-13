<?php

declare(strict_types=1);

namespace Andesk\EAF\Domain\Exceptions;

use InvalidArgumentException;

final class DoubleResolveException extends InvalidArgumentException
{
    protected $message = "Resolved object already set once, double setting is not supported/encouraged.";
}