<?php

declare(strict_types=1);

namespace App\Fixtures\Income;

use App\Customer\Entity\OperandId;
use App\Fixtures\Customer\OrganizationFixtures;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class IncomeFixtures extends Fixture
{
    public const ID = '1ea8f183-f4b0-6fe6-aa61-5e6bd0ab745f';
    public const SUPPLIER_ID = OrganizationFixtures::ID;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $income = new Income(
            IncomeId::from(self::ID),
            OperandId::from(self::SUPPLIER_ID),
            null,
        );

        $this->addReference('income-1', $income);

        $manager->persist($income);
        $manager->flush();
    }
}
