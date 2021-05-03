<?php

declare(strict_types=1);

namespace App\Manufacturer\View;

use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use App\Shared\Doctrine\Registry;
use LogicException;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;

final class ManufacturerFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $manufacturer = $this->registry->get(Manufacturer::class, $identifier);

        return $manufacturer->name ?? throw new LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return ManufacturerId::class;
    }
}
