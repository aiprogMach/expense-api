<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ExpenseDTO;
use App\Entity\Expense;
use App\Enum\ExpenseType;
use App\Exception\InvalidPropertyException;
use App\Service\Money\MoneyParser;
use InvalidArgumentException;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

class ExpenseValidator
{
    private const MAX_DESCRIPTION_LENGTH = 10000;

    private ISOCurrencies $currencies;

    private MoneyParser $moneyParser;

    public function __construct(MoneyParser $moneyParser)
    {
        $this->currencies = new ISOCurrencies();
        $this->moneyParser = $moneyParser;
    }

    /**
     * @throws InvalidPropertyException
     */
    public function validate(ExpenseDTO $expenseDTO)
    {
        $this->validateDescription($expenseDTO->getDescription());
        $this->validateCurrency($expenseDTO->getCurrency());
        $this->validatePrice($expenseDTO->getPrice(), $expenseDTO->getCurrency());
        $this->validateType($expenseDTO->getType());
    }

    public function validateDescription(?string $description)
    {
        $this->assertNotNull($description, 'description');
        if (strlen($description) === 0) {
            throw new InvalidPropertyException('Description cannot be empty');
        }

        if (strlen($description) > self::MAX_DESCRIPTION_LENGTH) {
            throw new InvalidPropertyException('Description cannot be empty');
        }
    }

    public function validatePrice(?string $price, ?string $currencyCode): void
    {
        $this->assertNotNull($price, 'price');
        $this->validateCurrency($currencyCode);

        $parsedPrice = $this->moneyParser->parse($price, $currencyCode);
        if ($parsedPrice <= 0) {
            throw new InvalidPropertyException('Price cannot be less than or equal to zero');
        }
    }

    public function validateCurrency(?string $currencyCode): void
    {
        $this->assertNotNull($currencyCode, 'currency');
        try {
            $currency = new Currency($currencyCode);
        } catch (InvalidArgumentException $e) {
            throw new InvalidPropertyException('Currency is not valid');
        }

        if (! $this->currencies->contains($currency)) {
            throw new InvalidPropertyException('Currency is not valid');
        }
    }

    public function validateType(?int $type): void
    {
        $this->assertNotNull($type, 'type');
        if (null === ExpenseType::tryFrom($type)) {
            throw new InvalidPropertyException('Type is not valid');
        }
    }

    /**
     * @throws InvalidPropertyException
     */
    private function assertNotNull($value, string $name): void
    {
        if (null === $value) {
            throw new InvalidPropertyException(sprintf('%s cannot be empty', $name));
        }
    }
}
