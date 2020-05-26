<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\MC\Entity\McEquipment;
use App\Vehicle\Entity\Model;
use App\Vehicle\Fixtures\NissanGTRFixture;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class EquipmentFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            NissanGTRFixture::class,
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
        $model = $this->getReference(NissanGTRFixture::class);
        assert($model instanceof Model);

        $equipment = new McEquipment();
        $equipment->model = $model;
        $equipment->period = 10;

        $this->addReference('equipment-1', $equipment);

        $manager->persist($equipment);
        $manager->flush();
    }
}
