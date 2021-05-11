<?php

declare(strict_types=1);

namespace App\MC\View;

use App\MC\Entity\McEquipment;
use App\MC\Entity\McEquipmentId;
use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
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
