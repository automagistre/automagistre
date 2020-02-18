<?php

declare(strict_types=1);

namespace App\Income\Fixtures;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Income;
use App\Entity\Tenant\IncomePart;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;

final class IncomePartFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
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
        $part = $this->registry->manager(Part::class)->getReference(Part::class, 1);
        $income = $this->getReference('income-1');
        assert($income instanceof Income);

        $incomePart = new IncomePart();
        $incomePart->setIncome($income);
        $incomePart->setPart($part);
        $incomePart->setPrice(new Money(100, new Currency('RUB')));
        $incomePart->setQuantity(100);

        $this->addReference('incomePart-1', $incomePart);

        $manager->persist($incomePart);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }
}
