<?php

declare(strict_types=1);

namespace App\Transformer;

use App\DTO\ExpenseDTO;
use App\Entity\Expense;
use App\Service\Money\MoneyFormatter;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class ExpenseToDTOTransformer
{
    private MoneyFormatter $moneyFormatter;

    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @param Expense[] $expenses
     *
     * @return ExpenseDTO[]
     */
    public function transform(array $expenses): array
    {
        return array_map(function (Expense $expense) {
            return $this->transformOne($expense);
        }, $expenses);
    }

    public function transformOne(Expense $expense): ExpenseDTO
    {
        return new ExpenseDTO(
            $expense->getId(),
            $expense->getDescription(),
            $this->moneyFormatter->formatToDecimal($expense->getPrice(), $expense->getCurrency()),
            $expense->getCurrency(),
            $expense->getType()
        );
    }
}
