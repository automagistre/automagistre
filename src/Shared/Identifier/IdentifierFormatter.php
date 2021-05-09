<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use LogicException;
use Premier\Identifier\Identifier;
use Psr\Container\ContainerInterface;
use function array_key_exists;
use function get_class;
use function sprintf;

final class IdentifierFormatter
{
    private array $map = [];

    public function __construct(private ContainerInterface $formatters)
    {
    }

    public function format(Identifier $identifier, string $format = null): string
    {
        $class = get_class($identifier);

        if (Identifier::class === $class) {
            throw new LogicException(sprintf('%s support only specific identifiers.', __CLASS__));
        }

        if (!$this->formatters->has($class)) {
            throw new LogicException(sprintf('Formatter for identifier "%s" not registered.', $class));
        }

        if (array_key_exists($identifier->toString(), $this->map)) {
            throw new LogicException(sprintf('Circular reference detected for %s with id %s', $class, $identifier->toString()));
        }

        $this->map[$identifier->toString()] = true;

        try {
            return $this->formatters->get($class)->format($this, $identifier, $format);
        } finally {
            unset($this->map[$identifier->toString()]);
        }
    }
}
