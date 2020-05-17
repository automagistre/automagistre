<?php

namespace App\Calendar\View;

use function implode;
use InvalidArgumentException;
use function sprintf;

final class StreamOverflowException extends InvalidArgumentException
{
    public static function fromKeys(array $keys): self
    {
        return new self(sprintf('Keys "%s" already defined.', implode(',', $keys)));
    }
}
