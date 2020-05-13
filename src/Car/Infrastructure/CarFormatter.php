<?php

declare(strict_types=1);

namespace App\Car\Infrastructure;

use App\Car\Entity\CarId;
use App\Doctrine\ORM\Type\Identifier;
use App\Doctrine\Registry;
use App\Infrastructure\Identifier\IdentifierFormatter;
use App\Infrastructure\Identifier\IdentifierFormatterInterface;
use App\Vehicle\Enum\DriveWheelConfiguration;
use App\Vehicle\Enum\Transmission;
use function assert;
use function implode;
use function sprintf;

final class CarFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->view($identifier);
        $string = '';

        $vehicle = $view['vehicleId'] ?? null;
        if (null !== $vehicle) {
            $string = $formatter->format($vehicle);
        }

        if ('' === $string) {
            return 'Не определено';
        }

        $year = $view['year'] ?? null;
        if (null !== $year) {
            $string .= sprintf(' - %sг.', $year);
        }

        if ('long' === $format) {
            $equipment = [];
            $equipment[] = $view['equipment.engine.name'];
            $equipment[] = $view['equipment.engine.capacity'];

            $transmission = $view['equipment.transmission'];
            assert($transmission instanceof Transmission);
            $equipment[] = $transmission->toCode();

            $wheelDrive = $view['equipment.wheelDrive'];
            assert($wheelDrive instanceof DriveWheelConfiguration);
            $equipment[] = $wheelDrive->toCode();

            $string .= ' '.implode(' ', $equipment);
        }

        return $string;
    }

    public static function support(): string
    {
        return CarId::class;
    }
}
