<?php

declare(strict_types=1);

namespace App\PartPrice\Fixtures;

use App\Part\Entity\PartId;
use App\Part\Fixtures\GasketFixture;
use App\PartPrice\Entity\Discount;
use App\PartPrice\Entity\Price;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class GasketPriceFixtures extends Fixture implements FixtureGroupInterface
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
        $partId = PartId::fromString(GasketFixture::ID);

        $manager->persist(new Price(
            $partId,
            new Money(GasketFixture::PRICE, new Currency(GasketFixture::PRICE_CURRENCY)),
            new DateTimeImmutable(),
        ));

        $manager->persist(new Discount(
            $partId,
            new Money(GasketFixture::DISCOUNT, new Currency(GasketFixture::DISCOUNT_CURRENCY)),
            new DateTimeImmutable(),
        ));

        $manager->flush();
    }
}
