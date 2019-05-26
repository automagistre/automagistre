<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LogicException extends \LogicException
{
    public static function unexpected(string $type, string $value): self
    {
        return new self(\sprintf('Unexpected %s "%s".', $type, $value));
    }

    public static function mustImplement(object $object, string $implement): self
    {
        return new self(\sprintf('%s must implement %s', \get_class($object), $implement));
    }
}
