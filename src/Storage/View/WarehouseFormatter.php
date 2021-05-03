<?php

declare(strict_types=1);

namespace App\Storage\View;

use App\Shared\Doctrine\Registry;
use Premier\Identifier\Identifier;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseView;

final class WarehouseFormatter implements IdentifierFormatterInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public static function support(): string
    {
        return WarehouseId::class;
    }

    public function format(IdentifierFormatter $formatter, Identifier $identifier, string $format = null): string
    {
        $view = $this->registry->getBy(WarehouseView::class, $identifier);

        return $view->name;
    }
}
