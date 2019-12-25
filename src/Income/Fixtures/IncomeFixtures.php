<?php

declare(strict_types=1);

namespace App\Income\Fixtures;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Income;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class IncomeFixtures extends Fixture implements FixtureGroupInterface
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $operand = $this->registry->manager(Operand::class)->getReference(Operand::class, 1);
        assert($operand instanceof Operand);

        $income = new Income();
        $income->setSupplier($operand);

        $this->addReference('income-1', $income);

        $manager->persist($income);
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
