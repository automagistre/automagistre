<?php

declare(strict_types=1);

namespace App\MC\View;

use App\MC\Entity\McEquipment;
use App\MC\Entity\McEquipmentId;
use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use function sprintf;

final class McEquipmentFormatter implements IdentifierFormatterInterface
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
        $view = $this->registry->get(McEquipment::class, $identifier);

        return sprintf(
            '%s %s',
            $formatter->format($view->vehicleId),
            $view->equipment->toString()
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
