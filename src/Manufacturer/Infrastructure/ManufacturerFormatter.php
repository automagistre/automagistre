<?php

declare(strict_types=1);

namespace App\Manufacturer\Infrastructure;

use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Infrastructure\Identifier\IdentifierFormatter;
use App\Infrastructure\Identifier\IdentifierFormatterInterface;
use App\Manufacturer\Domain\ManufacturerId;

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
        $view = $this->registry->view($identifier);

        return $view['name'];
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return ManufacturerId::class;
    }
}
