<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\Entity\Landlord\MC\Equipment;
use App\Entity\Landlord\MC\Line;
use App\Entity\Landlord\MC\Part;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PartFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            LineFixtures::class,
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
        $line = $this->getReference('line-1');
        assert($line instanceof Line);
        $part = $this->getReference('part-1');
        assert($part instanceof \App\Entity\Landlord\Part);

        $mcPart = new Part($line, $part, 1, false);

        $this->addReference('mc-part-1', $mcPart);

        $manager->persist($mcPart);
        $manager->flush();
    }
}
