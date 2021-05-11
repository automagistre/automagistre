<?php

declare(strict_types=1);

namespace App\Fixtures\Manufacturer;

use App\Manufacturer\Entity\Manufacturer;
use App\Manufacturer\Entity\ManufacturerId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class NissanFixture extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea88058-1c1f-6f20-9482-ba1ca6d07248';
    public const NAME = 'Nissan';
    public const LOCALIZED_NAME = 'Ниссан';

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [LexusFixture::class];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $manufacturer = new Manufacturer(
            ManufacturerId::from(self::ID),
            self::NAME,
            self::LOCALIZED_NAME,
        );

        $this->addReference(__CLASS__, $manufacturer);

        $manager->persist($manufacturer);
        $manager->flush();
    }
}
