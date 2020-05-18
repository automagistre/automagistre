<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use function array_key_exists;
use function get_class;
use LogicException;
use Psr\Container\ContainerInterface;
use function sprintf;

final class IdentifierFormatter
{
    private ContainerInterface $formatters;

    private array $map = [];

    public function __construct(ContainerInterface $formatters)
    {
        $this->formatters = $formatters;
    }

    public function format(Identifier $identifier, string $format = null): string
    {
        if (Identifier::class === get_class($identifier)) {
            throw new LogicException(sprintf('%s support only specific identifiers.', __CLASS__));
        }

        $formatter = $this->formatters->get(get_class($identifier));
        if (!$formatter instanceof IdentifierFormatterInterface) {
            throw new LogicException(sprintf('Formatter for identifier "%s" not registered.', get_class($identifier)));
        }

        if (array_key_exists($identifier->toString(), $this->map)) {
            throw new LogicException(sprintf('Circular reference detected for %s with id %s', get_class($identifier), $identifier->toString()));
        }

        $this->map[$identifier->toString()] = true;

        try {
            return $formatter->format($this, $identifier, $format);
        } finally {
            unset($this->map[$identifier->toString()]);
        }
    }
}
