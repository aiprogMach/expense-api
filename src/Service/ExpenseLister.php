<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ExpenseDTO;
use App\DTO\PaginatedExpenseDTO;
use App\Repository\ExpenseRepository;
use App\Transformer\ExpenseToDTOTransformer;
use InvalidArgumentException;

class ExpenseLister
{
    private ExpenseRepository $expenseRepository;

    private ExpenseToDTOTransformer $expenseToDTOTransformer;

    public function __construct(ExpenseRepository $expenseRepository, ExpenseToDTOTransformer $expenseToDTOTransformer)
    {
        $this->expenseRepository = $expenseRepository;
        $this->expenseToDTOTransformer = $expenseToDTOTransformer;
    }

    /**
     * @return ExpenseDTO[]
     * 
     * @throws InvalidArgumentException
     */
    public function list(int $page = 0, int $countPerPage = 100): PaginatedExpenseDTO
    {
        if ($page < 0) {
            throw new InvalidArgumentException('Page cannot be negative');
        }

        if ($countPerPage > 100 || $countPerPage < 1) {
            throw new InvalidArgumentException('Count per page must be between 1 and 100');
        }

        $expensesPaginator = $this->expenseRepository->findAllWithPagination($page, $countPerPage);
        $dtos = $this->expenseToDTOTransformer->transform($expensesPaginator->getIterator()->getArrayCopy());
        $totalCount = count($expensesPaginator);

        return new PaginatedExpenseDTO(
            $page,
            $countPerPage,
            $totalCount,
            (int) ceil($totalCount / $countPerPage),
            $dtos
        );
    }
}
