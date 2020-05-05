<?php

declare(strict_types=1);

namespace App\Income\Fixtures;

use App\Customer\Domain\Operand;
use App\Doctrine\Registry;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\User\Fixtures\UserRelationFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class IncomeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const ID = '1ea8f183-f4b0-6fe6-aa61-5e6bd0ab745f';

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

        $income = new Income(
            IncomeId::fromString(self::ID),
        );
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
