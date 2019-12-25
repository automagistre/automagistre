<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\Car\Entity\Model;
use App\Entity\Landlord\MC\Equipment;
use App\Entity\Landlord\MC\Work;
use App\Manufacturer\Entity\Manufacturer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class WorkFixtures extends Fixture implements FixtureGroupInterface
{
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
        $work = new Work();
        $work->name = 'Work 1';
        $work->price = new Money(100, new Currency('RUB'));

        $this->addReference('work-1', $work);

        $manager->persist($work);
        $manager->flush();
    }
}
