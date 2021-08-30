<?php

declare(strict_types=1);

namespace App\MC\View;

use App\Doctrine\Registry;
use App\Identifier\IdentifierFormatter;
use App\Identifier\IdentifierFormatterInterface;
use App\MC\Entity\McEquipment;
use App\MC\Entity\McEquipmentId;
use Premier\Identifier\Identifier;
use function sprintf;

final class McEquipmentFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->get(McEquipment::class, $identifier);

        return sprintf(
            '%s %s',
            $formatter->format($view->vehicleId),
            $view->equipment->toString(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return McEquipmentId::class;
    }
}
