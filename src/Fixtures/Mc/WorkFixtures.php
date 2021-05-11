<?php

declare(strict_types=1);

namespace App\Fixtures\Mc;

use App\MC\Entity\McWork;
use App\MC\Entity\McWorkId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class WorkFixtures extends Fixture
{
    public const ID = '1eab7c27-bda7-656e-8203-0242c0a81005';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $work = new McWork(
            McWorkId::from(self::ID),
            'Work 1',
            null,
            new Money(100, new Currency('RUB')),
            null,
        );

        $this->addReference('work-1', $work);

        $manager->persist($work);
        $manager->flush();
    }
}
