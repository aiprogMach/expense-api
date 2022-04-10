<?php

namespace App\Tests;

use App\Entity\Expense;
use App\Service\Money\MoneyFormatter;
use App\Transformer\ExpenseToDTOTransformer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ExpenseToDTOTransformerTest extends TestCase
{
    use ProphecyTrait;

    private $moneyFormatter;

    private $expense;

    private ExpenseToDTOTransformer $transformer;

    protected function setUp(): void
    {
        $this->moneyFormatter = $this->prophesize(MoneyFormatter::class);
        $this->expense = $this->prophesize(Expense::class);
        $this->expense->getId()->willReturn(1);
        $this->expense->getDescription()->willReturn('description');
        $this->expense->getPrice()->willReturn(1);
        $this->expense->getCurrency()->willReturn('EUR');
        $this->expense->getType()->willReturn(1);

        $this->moneyFormatter->formatToDecimal(1, 'EUR')->willReturn('100');

        $this->transformer = new ExpenseToDTOTransformer($this->moneyFormatter->reveal());
    }

    public function testExpense(): void
    {
        $dto = $this->transformer->transformOne($this->expense->reveal());

        static::assertSame(1, $dto->getId());
        static::assertSame('description', $dto->getDescription());
        static::assertSame('100', $dto->getPrice());
        static::assertSame('EUR', $dto->getCurrency());
        static::assertSame(1, $dto->getType());
    }

    public function testExpenses(): void
    {
        $dtos = $this->transformer->transform([$this->expense->reveal()]);
        $dto = current($dtos);

        static::assertCount(1, $dtos);
        static::assertSame(1, $dto->getId());
        static::assertSame('description', $dto->getDescription());
        static::assertSame('100', $dto->getPrice());
        static::assertSame('EUR', $dto->getCurrency());
        static::assertSame(1, $dto->getType());
    }
}
