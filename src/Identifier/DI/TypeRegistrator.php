<?php

declare(strict_types=1);

namespace App\Identifier\DI;

use Doctrine\DBAL\Types\Type;
use Premier\Identifier\Doctrine\IdentifierType;
use Premier\Identifier\Identifier;

final class TypeRegistrator
{
    /**
     * @psalm-param class-string<Identifier>[] $classes
     */
    public function __construct(array $classes)
    {
        foreach ($classes as $class) {
            if (!Type::hasType($class)) {
                IdentifierType::register($class, $class);
            }
        }
    }
}
