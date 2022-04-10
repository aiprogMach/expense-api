<?php

namespace App\Tests;

use App\DTO\ExpenseDTO;
use App\DTO\PaginatedExpenseDTO;
use App\Repository\ExpenseRepository;
use App\Service\ExpenseLister;
use App\Transformer\ExpenseToDTOTransformer;
use ArrayIterator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ExpenseListerTest extends TestCase
{
    use ProphecyTrait;

    private $expenseRepository;

    private $expenseToDTOTransformer;

    private ExpenseLister $expenseLister;

    protected function setUp(): void
    {
        $this->expenseRepository = $this->prophesize(ExpenseRepository::class);
        $this->expenseToDTOTransformer = $this->prophesize(ExpenseToDTOTransformer::class);

        $this->expenseLister = new ExpenseLister(
            $this->expenseRepository->reveal(),
            $this->expenseToDTOTransformer->reveal()
        );
    }

    public function testPageNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expenseLister->list(-1, 10);
    }

    public function testCountPerPageZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expenseLister->list(1, 0);
    }

    public function testCountPerPageMoreThan100(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->expenseLister->list(1, 101);
    }

    public function testSuccess(): void
    {
        $paginator = $this->prophesize(Paginator::class);
        $expense = $this->prophesize(Expense::class)->reveal();
        $expenseDTO = new ExpenseDTO(1, 'description', '100', 'EUR', 1);
        $this->expenseRepository->findAllWithPagination(0, 10)->willReturn($paginator->reveal());
        $this->expenseToDTOTransformer->transform([$expense])->willReturn([$expenseDTO]);

        $arrayIterator = new ArrayIterator([$expense]);
        $paginator->getIterator()->willReturn($arrayIterator);
        $paginator->count()->willReturn(1);

        $result = new PaginatedExpenseDTO(0, 10, 1, 1, [$expenseDTO]);

        static::assertEquals($result, $this->expenseLister->list(0, 10));
    }
}
