<?php

declare(strict_types=1);

namespace App\Vehicle\View;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use Premier\Identifier\Identifier;
use function sprintf;

final class VehicleFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $vehicle = $this->registry->get(Model::class, $identifier);

        $text = sprintf(
            '%s %s',
            $formatter->format($vehicle->manufacturerId),
            $vehicle->name,
        );

        $case = $vehicle->caseName;

        if (null !== $case) {
            $text .= sprintf(' - %s', $case);
        }

        $from = $vehicle->yearFrom;
        $till = $vehicle->yearTill;

        if ('long' === $format && (null !== $from || null !== $till)) {
            $text .= sprintf(' (%s - %s)', $from ?? '...', $till ?? '...');
        }

        return $text;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return VehicleId::class;
    }
}
