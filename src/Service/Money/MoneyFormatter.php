<?php

declare(strict_types=1);

namespace App\Service\Money;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

class MoneyFormatter
{
    public function formatToDecimal(int $priceInSubunit, string $currency): string
    {
        $money = new Money($priceInSubunit, new Currency(strtolower($currency)));

        $currencies = new ISOCurrencies();

        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return $moneyFormatter->format($money);
    }
}
