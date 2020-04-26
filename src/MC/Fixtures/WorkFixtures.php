<?php

declare(strict_types=1);

namespace App\MC\Fixtures;

use App\Entity\Landlord\MC\Work;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
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
        $work = new Work('Work 1', null, new Money(100, new Currency('RUB')));

        $this->addReference('work-1', $work);

        $manager->persist($work);
        $manager->flush();
    }
}
