<?php

declare(strict_types=1);

namespace App\Wallet\Fixtures;

use App\Entity\Tenant\Wallet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;

final class WalletFixtures extends Fixture implements FixtureGroupInterface
{
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
        $wallet = new Wallet();
        $wallet->name = 'Main';
        $wallet->currency = new Currency('RUB');

        $this->addReference('wallet-1', $wallet);

        $manager->persist($wallet);
        $manager->flush();
    }
}
