<?php

declare(strict_types=1);

namespace App\Income\Fixtures;

use App\Doctrine\Registry;
use App\Entity\Landlord\Operand;
use App\Entity\Tenant\Income;
use App\User\Fixtures\UserRelationFixtures;
use function assert;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class IncomeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
            UserRelationFixtures::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $operand = $this->registry->manager(Operand::class)->getReference(Operand::class, 1);

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
