<?php

declare(strict_types=1);

namespace App\Fixtures\Mc;

use App\MC\Entity\McEquipment;
use App\MC\Entity\McLine;
use App\MC\Entity\McWork;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use function assert;

final class LineFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1eab7ada-800b-69d8-a8a9-0242c0a81005';

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
    public function load(ObjectManager $manager): void
    {
        $equipment = $this->getReference('equipment-1');
        assert($equipment instanceof McEquipment);
        $work = $this->getReference('work-1');
        assert($work instanceof McWork);

        $line = new McLine(
            Uuid::fromString(self::ID),
            $equipment,
            $work,
            10,
            false
        );

        $this->addReference('line-1', $line);

        $manager->persist($line);
        $manager->flush();
    }
}
