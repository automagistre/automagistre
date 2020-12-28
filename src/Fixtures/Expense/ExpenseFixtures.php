<?php

declare(strict_types=1);

namespace App\Fixtures\Expense;

use App\Expense\Entity\Expense;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ExpenseFixtures extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager): void
    {
        $expense = new Expense('ЖКХ');

        $this->addReference('expense-1', $expense);

        $manager->persist($expense);
        $manager->flush();
    }
}
