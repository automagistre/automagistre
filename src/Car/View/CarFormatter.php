<?php

declare(strict_types=1);

namespace App\Car\View;

use App\Car\Entity\CarId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Vehicle\Enum\DriveWheelConfiguration;
use App\Vehicle\Enum\Transmission;
use function array_keys;
use function array_values;
use function assert;
use function implode;
use function str_replace;

final class CarFormatter implements IdentifierFormatterInterface
{
    private const DEFAULT = ':vehicle: - :year:г.';
    private const FORMATS = [
        'short' => ':vehicle:',
        'long' => ':vehicle: | :equipment: | :gosnomer:',
    ];

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->view($identifier);
        $vehicle = $view['vehicleId'] ?? null;
        if (null === $vehicle) {
            return 'Не определено';
        }

        $values = [
            ':year:' => $view['year'],
            ':gosnomer:' => $view['gosnomer'],
            ':vehicle:' => $formatter->format($vehicle),
        ];

        $values[':equipment:'] = (static function (array $view): string {
            $equipment = [];
            $equipment[] = $view['equipment.engine.name'];
            $equipment[] = $view['equipment.engine.capacity'];

            $transmission = $view['equipment.transmission'];
            assert($transmission instanceof Transmission);
            $equipment[] = $transmission->toCode();

            $wheelDrive = $view['equipment.wheelDrive'];
            assert($wheelDrive instanceof DriveWheelConfiguration);
            $equipment[] = $wheelDrive->toCode();

            return implode(' ', $equipment);
        })($view);

        return str_replace(array_keys($values), array_values($values), self::FORMATS[$format] ?? self::DEFAULT);
    }

    public static function support(): string
    {
        return CarId::class;
    }
}
