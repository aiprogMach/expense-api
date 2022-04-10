<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Expense;
use App\Enum\ExpenseType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExpenseFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $expense = new Expense();
        $expense->setDescription('Expense 1');
        $expense->setPrice(1000);
        $expense->setCurrency('EUR');
        $expense->setType(ExpenseType::Food->value);

        $manager->persist($expense);

        $manager->flush();
    }
}
