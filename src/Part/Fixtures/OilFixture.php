<?php

declare(strict_types=1);

namespace App\Part\Fixtures;

use App\Manufacturer\Entity\ManufacturerId;
use App\Manufacturer\Fixtures\ToyotaFixture;
use App\Part\Entity\Discount;
use App\Part\Entity\Part;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use App\Part\Entity\Price;
use App\Part\Enum\Unit;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class OilFixture extends Fixture
{
    public const ID = '1eae88c9-3657-6472-9992-0242c0a8100b';
    public const MANUFACTURER_ID = ToyotaFixture::ID;
    public const NAME = 'Масло';
    public const NUMBER = 'OIL';
    public const IS_UNIVERSAL = false;
    public const PRICE = 150000;
    public const PRICE_CURRENCY = 'RUB';
    public const DISCOUNT = 10000;
    public const DISCOUNT_CURRENCY = 'RUB';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $partId = PartId::fromString(self::ID);

        $part = new Part(
            $partId,
            ManufacturerId::fromString(self::MANUFACTURER_ID),
            self::NAME,
            new PartNumber(self::NUMBER),
            self::IS_UNIVERSAL,
            Unit::liter(),
        );

        $this->addReference('part-2', $part);

        $manager->persist(new Price(
            $partId,
            new Money(self::PRICE, new Currency(self::PRICE_CURRENCY)),
            new DateTimeImmutable(),
        ));

        $manager->persist(new Discount(
            $partId,
            new Money(self::DISCOUNT, new Currency(self::DISCOUNT_CURRENCY)),
            new DateTimeImmutable(),
        ));

        $manager->persist($part);
        $manager->flush();
    }
}
