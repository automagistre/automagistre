<?php

declare(strict_types=1);

namespace App\Part\Fixtures;

use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Fixtures\ToyotaFixture;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class GasketFixture extends Fixture implements FixtureGroupInterface
{
    public const ID = '1ea88126-9b50-62f8-9995-ba1ca6d07248';
    public const MANUFACTURER_ID = ToyotaFixture::ID;
    public const NAME = 'Сальник';
    public const NUMBER = 'PART1NUMBER';
    public const IS_UNIVERSAL = false;
    public const PRICE = 150000;
    public const PRICE_CURRENCY = 'RUB';
    public const DISCOUNT = 10000;
    public const DISCOUNT_CURRENCY = 'RUB';

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
        $part = new Part(
            PartId::fromString(self::ID),
            ManufacturerId::fromString(self::MANUFACTURER_ID),
            self::NAME,
            new PartNumber(self::NUMBER),
            self::IS_UNIVERSAL,
            new Money(self::PRICE, new Currency(self::PRICE_CURRENCY)),
            new Money(self::DISCOUNT, new Currency(self::DISCOUNT_CURRENCY)),
        );

        $this->addReference('part-1', $part);

        $manager->persist($part);
        $manager->flush();
    }
}
