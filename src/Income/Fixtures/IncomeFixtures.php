<?php

declare(strict_types=1);

namespace App\Income\Fixtures;

use App\Customer\Entity\Operand;
use App\Customer\Entity\OperandId;
use App\Customer\Fixtures\OrganizationFixtures;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Shared\Doctrine\Registry;
use App\User\Fixtures\UserRelationFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class IncomeFixtures extends Fixture implements DependentFixtureInterface
{
    public const ID = '1ea8f183-f4b0-6fe6-aa61-5e6bd0ab745f';
    public const SUPPLIER_ID = OrganizationFixtures::ID;

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
            OperandId::fromString(self::SUPPLIER_ID),
            null
        );

        $this->addReference('income-1', $income);

        $manager->persist($income);
        $manager->flush();
    }
}
