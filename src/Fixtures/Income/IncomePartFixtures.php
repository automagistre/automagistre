<?php

declare(strict_types=1);

namespace App\Fixtures\Income;

use App\Fixtures\Part\GasketFixture;
use App\Income\Entity\Income;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Part\Entity\PartId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;
use function assert;

final class IncomePartFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1eab7ce2-766c-6db4-85d8-0242c0a81005';
    public const PART_ID = GasketFixture::ID;

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            IncomeFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $income = $this->getReference('income-1');
        assert($income instanceof Income);

        $incomePart = new IncomePart(
            IncomePartId::fromString(self::ID),
            $income,
        );
        $incomePart->partId = PartId::fromString(self::PART_ID);
        $incomePart->setPrice(new Money(100, new Currency('RUB')));
        $incomePart->setQuantity(100);

        $this->addReference('incomePart-1', $incomePart);

        $manager->persist($incomePart);
        $manager->flush();
    }
}
