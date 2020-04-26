<?php

declare(strict_types=1);

namespace App\Part\Fixtures;

use App\Manufacturer\Domain\Manufacturer;
use App\Manufacturer\Infrastructure\Fixtures\NissanFixture;
use App\Part\Domain\Part;
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
            NissanFixture::class,
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
        $manufacturer = $this->getReference(NissanFixture::REF);
        assert($manufacturer instanceof Manufacturer);

        $part = new Part($manufacturer, 'Part 1', 'part1number', false, null);

        $this->addReference('part-1', $part);
        $manager->persist($part);
        $manager->flush();
    }
}
