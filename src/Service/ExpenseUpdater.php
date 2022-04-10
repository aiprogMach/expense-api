<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ExpenseDTO;
use App\Entity\Expense;
use App\Service\Money\MoneyParser;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseUpdater
{
    private MoneyParser $moneyParser;

    private ExpenseValidator $expenseValidator;

    private EntityManagerInterface $entityManager;

    public function __construct(MoneyParser $moneyParser, ExpenseValidator $expenseValidator, EntityManagerInterface $entityManager)
    {
        $this->moneyParser = $moneyParser;
        $this->expenseValidator = $expenseValidator;
        $this->entityManager = $entityManager;
    }

    public function update(Expense $expense, ExpenseDTO $expenseDTO): Expense
    {
        if (null !== $expenseDTO->getDescription()) {
            $this->expenseValidator->validateDescription($expenseDTO->getDescription());
            $expense->setDescription($expenseDTO->getDescription());
        }

        if (null !== $expenseDTO->getPrice()) {
            $this->expenseValidator->validatePrice($expenseDTO->getPrice(), $expenseDTO->getCurrency());
            $expense->setPrice($this->moneyParser->parse($expenseDTO->getPrice(), $expenseDTO->getCurrency()));
        }

        if (null !== $expenseDTO->getCurrency()) {
            $this->expenseValidator->validateCurrency($expenseDTO->getCurrency());
            $expense->setCurrency($expenseDTO->getCurrency());
        }

        if (null !== $expenseDTO->getType()) {
            $this->expenseValidator->validateType($expenseDTO->getType());
            $expense->setType($expenseDTO->getType());
        }

        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        return $expense;
    }
}
