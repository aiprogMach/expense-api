<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ExpenseDTO;
use App\Entity\Expense;
use App\Enum\ExpenseType;
use App\Exception\InvalidPropertyException;
use App\Service\Money\MoneyParser;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

class ExpenseCreator
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

    /**
     * @throws InvalidPropertyException
     */
    public function create(ExpenseDTO $expenseDTO): Expense
    {
        $this->expenseValidator->validate($expenseDTO);

        $expense = new Expense();
        $expense->setDescription($expenseDTO->getDescription());
        $expense->setPrice($this->moneyParser->parse($expenseDTO->getPrice(), $expenseDTO->getCurrency()));
        $expense->setCurrency($expenseDTO->getCurrency());
        $expense->setType($expenseDTO->getType());

        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        return $expense;
    }
}
