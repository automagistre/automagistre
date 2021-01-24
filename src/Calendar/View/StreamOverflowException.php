<?php

declare(strict_types=1);

namespace App\Calendar\View;

use InvalidArgumentException;
use function implode;
use function sprintf;

final class StreamOverflowException extends InvalidArgumentException
{
    public static function fromKeys(array $keys): self
    {
        return new self(sprintf('Keys "%s" already defined.', implode(',', $keys)));
    }
}
