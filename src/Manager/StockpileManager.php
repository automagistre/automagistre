<?php

declare(strict_types=1);

namespace App\Manager;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use function array_map;
use function implode;
use function sprintf;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class StockpileManager
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function actualize(array $values): void
    {
        $valueString = implode(',', array_map(static function (array $item): string {
            [$partId, $tenant, $quantity] = $item;

            return sprintf('(%s, %s, %s)', $partId, $tenant, $quantity);
        }, $values));

        $sql = "INSERT INTO stockpile (part_id, tenant, quantity) VALUES {$valueString} ON CONFLICT (part_id, tenant) DO UPDATE SET quantity=EXCLUDED.quantity";

        $this->registry->manager(Part::class)->getConnection()->executeQuery($sql);
    }
}
