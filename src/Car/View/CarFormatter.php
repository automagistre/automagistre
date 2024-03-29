<?php

declare(strict_types=1);

namespace App\Car\View;

use App\Car\Entity\Car;
use App\Car\Entity\CarId;
use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use Premier\Identifier\Identifier;
use function implode;
use function strtr;

final class CarFormatter implements IdentifierFormatterInterface
{
    private const DEFAULT = ':vehicle: - :year:г.';
    private const FORMATS = [
        'short' => ':vehicle:',
        'autocomplete' => ':vehicle: | :gosnomer:',
        'long' => ':vehicle: | :equipment: | :gosnomer:',
    ];

    public function __construct(private Registry $registry)
    {
    }

    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $car = $this->registry->get(Car::class, $identifier);
        $vehicle = $car->vehicleId;

        if (null === $vehicle) {
            return 'Не определено';
        }

        $values = [
            ':year:' => $car->year,
            ':gosnomer:' => $car->getGosnomer(),
            ':vehicle:' => $formatter->format($vehicle),
        ];

        $values[':equipment:'] = (static function (Car $car): string {
            $equipment = [];
            $equipment[] = $car->equipment->engine->name;
            $equipment[] = $car->equipment->engine->capacity;
            $equipment[] = $car->equipment->transmission->toCode();
            $equipment[] = $car->equipment->wheelDrive->toCode();

            return implode(' ', $equipment);
        })($car);

        return strtr(self::FORMATS[$format] ?? self::DEFAULT, $values);
    }

    public static function support(): string
    {
        return CarId::class;
    }
}
