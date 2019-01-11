<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StockpileManager
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function actualize(array $values): void
    {
        $values = \implode(',', \array_map(function (array $item) {
            [$partId, $tenant, $quantity] = $item;

            return \sprintf('(%s, %s, %s)', $partId, $tenant, $quantity);
        }, $values));

        $sql = "INSERT INTO stockpile (part_id, tenant, quantity) VALUES {$values} ON DUPLICATE KEY UPDATE quantity=VALUES(quantity)";

        $this->registry->manager(Part::class)->getConnection()->executeQuery($sql);
    }
}
