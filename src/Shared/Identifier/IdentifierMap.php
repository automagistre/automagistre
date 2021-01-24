<?php

declare(strict_types=1);

namespace App\Shared\Identifier;

use InvalidArgumentException;
use function array_key_exists;
use function get_class;
use function sprintf;

final class IdentifierMap
{
    /**
     * @var array<class-string<Identifier>, class-string>
     */
    private array $map;

    /**
     * @psalm-param  array<class-string<Identifier>, class-string> $map
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    /**
     * @psalm-return class-string
     */
    public function entityClassByIdentifier(Identifier $identifier): string
    {
        $class = get_class($identifier);

        if (!array_key_exists($class, $this->map)) {
            throw new InvalidArgumentException(sprintf('Not found entity class for identifier class %s', $class));
        }

        return $this->map[$class];
    }
}
