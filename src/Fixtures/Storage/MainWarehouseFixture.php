<?php

declare(strict_types=1);

namespace App\Fixtures\Storage;

use App\Storage\Entity\Warehouse;
use App\Storage\Entity\WarehouseCode;
use App\Storage\Entity\WarehouseId;
use App\Storage\Entity\WarehouseName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class MainWarehouseFixture extends Fixture
{
    public const ID = '1eaa75a7-7c4e-6360-8ee5-0242ac1c0002';
    public const NAME = 'Основной';
    public const CODE = 'О';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $warehouseId = WarehouseId::from(self::ID);

        $manager->persist(new Warehouse($warehouseId));
        $manager->persist(new WarehouseName($warehouseId, self::NAME));
        $manager->persist(new WarehouseCode($warehouseId, self::NAME));

        $manager->flush();
    }
}
