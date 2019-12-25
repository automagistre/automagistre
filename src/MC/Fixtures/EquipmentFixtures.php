<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\Car\Entity\Model;
use App\Entity\Landlord\MC\Equipment;
use App\Vehicle\Fixtures\ModelFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class EquipmentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            ModelFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['landlord'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $model = $this->getReference('model-1');
        assert($model instanceof Model);

        $equipment = new Equipment();
        $equipment->model = $model;
        $equipment->period = 10;

        $this->addReference('equipment-1', $equipment);

        $manager->persist($equipment);
        $manager->flush();
    }
}
