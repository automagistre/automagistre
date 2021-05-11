<?php

declare(strict_types=1);

namespace App\Fixtures\Part;

use App\Fixtures\Manufacturer\ToyotaFixture;
use App\Manufacturer\Entity\ManufacturerId;
use App\Part\Entity\Discount;
use App\Part\Entity\Part;
use App\Part\Entity\PartCross;
use App\Part\Entity\PartId;
use App\Part\Entity\PartNumber;
use App\Part\Entity\Price;
use App\Part\Enum\Unit;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class GasketFixture extends Fixture implements DependentFixtureInterface
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
    public function getDependencies(): array
    {
        return [
            OilFixture::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $partId = PartId::from(self::ID);

        $part = new Part(
            $partId,
            ManufacturerId::from(self::MANUFACTURER_ID),
            self::NAME,
            new PartNumber(self::NUMBER),
            self::IS_UNIVERSAL,
            Unit::thing(),
        );

        $this->addReference('part-1', $part);

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

        /** @var Part $gasket */
        $gasket = $this->getReference('part-2');
        $manager->persist(new PartCross($part, $gasket));

        $manager->persist($part);
        $manager->flush();
    }
}
