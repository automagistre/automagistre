<?php

declare(strict_types=1);

namespace App\Storage\View;

use App\Shared\Doctrine\Registry;
use App\Shared\Identifier\IdentifierFormatter;
use App\Shared\Identifier\IdentifierFormatterInterface;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseView;
use Premier\Identifier\Identifier;

final class WarehouseFormatter implements IdentifierFormatterInterface
{
    public function __construct(private Registry $registry)
    {
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
