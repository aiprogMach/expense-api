<?php

declare(strict_types=1);

namespace App\Service\Money;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class MoneyParser
{
    public function parse(string $price, string $currency): int
    {
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);
        $money = $moneyParser->parse($price, new Currency($currency));

        return (int) $money->getAmount();
    }
}
