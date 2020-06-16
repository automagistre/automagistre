<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\MC\Entity\McEquipment;
use App\MC\Entity\McLine;
use App\MC\Entity\McWork;
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
        return ['tenant'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $equipment = $this->getReference('equipment-1');
        assert($equipment instanceof McEquipment);
        $work = $this->getReference('work-1');
        assert($work instanceof McWork);

        $line = new McLine($equipment, $work, 10, false);

        $this->addReference('line-1', $line);

        $manager->persist($line);
        $manager->flush();
    }
}
