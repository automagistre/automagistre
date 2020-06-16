<?php

declare(strict_types=1);

namespace App\Wallet\Fixtures;

use App\Wallet\Entity\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;

final class WalletFixtures extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $wallet = new Wallet('Main', new Currency('RUB'));

        $this->addReference('wallet-1', $wallet);

        $manager->persist($wallet);
        $manager->flush();
    }
}
