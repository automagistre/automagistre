<?php

declare(strict_types=1);

namespace App\Identifier;

use DateTimeImmutable;
use LogicException;
use Premier\Identifier\Identifier;
use Psr\Cache\CacheItemInterface;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use function array_key_exists;
use function sprintf;

final class IdentifierFormatter
{
    private array $map = [];

    public function __construct(
        private ContainerInterface $formatters,
        private CacheInterface $cache,
    ) {
    }

    public function format(Identifier $identifier, string $format = null): string
    {
        $key = $identifier->toString().$format;

        return $this->cache->get($key, function (CacheItemInterface $item) use ($identifier, $format): string {
            $item->expiresAt(new DateTimeImmutable('+15 minutes'));

            return $this->doFormat($identifier, $format);
        });
    }

    private function doFormat(Identifier $identifier, ?string $format): string
    {
        $class = $identifier::class;

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
