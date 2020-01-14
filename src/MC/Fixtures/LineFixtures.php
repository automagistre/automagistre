<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\Entity\Landlord\MC\Equipment;
use App\Entity\Landlord\MC\Line;
use App\Entity\Landlord\MC\Work;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LineFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            EquipmentFixtures::class,
            WorkFixtures::class,
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
        $equipment = $this->getReference('equipment-1');
        assert($equipment instanceof Equipment);
        $work = $this->getReference('work-1');
        assert($work instanceof Work);

        $line = new Line();
        $line->equipment = $equipment;
        $line->work = $work;
        $line->period = 10;

        $this->addReference('line-1', $line);

        $manager->persist($line);
        $manager->flush();
    }
}
