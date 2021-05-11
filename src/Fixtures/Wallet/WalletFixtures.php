<?php

declare(strict_types=1);

namespace App\Fixtures\Wallet;

use App\Wallet\Entity\Wallet;
use App\Wallet\Entity\WalletId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;

final class WalletFixtures extends Fixture
{
    public const ID = '1eab1a10-5d2d-6734-a594-0242ac1c000a';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $wallet = new Wallet(
            WalletId::from(self::ID),
            'Main',
            new Currency('RUB'),
        );

        $this->addReference('wallet-1', $wallet);

        $manager->persist($wallet);
        $manager->flush();
    }
}
