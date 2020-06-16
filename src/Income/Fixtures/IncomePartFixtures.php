<?php

declare(strict_types=1);

namespace App\Income\Fixtures;

use App\Income\Entity\Income;
use App\Income\Entity\IncomePart;
use App\Part\Entity\PartId;
use App\Part\Fixtures\GasketFixture;
use App\Shared\Doctrine\Registry;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class IncomePartFixtures extends Fixture implements DependentFixtureInterface
{
    private const PART_ID = GasketFixture::ID;

    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

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

        $incomePart = new IncomePart();
        $incomePart->setIncome($income);
        $incomePart->partId = PartId::fromString(self::PART_ID);
        $incomePart->setPrice(new Money(100, new Currency('RUB')));
        $incomePart->setQuantity(100);

        $this->addReference('incomePart-1', $incomePart);

        $manager->persist($incomePart);
        $manager->flush();
    }
}
