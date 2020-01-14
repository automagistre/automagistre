<?php

declare(strict_types=1);

namespace App\Expense\Fixtures;

use App\Entity\Tenant\Expense;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class ExpenseFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getGroups(): array
    {
        return ['tenant'];
    }

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
