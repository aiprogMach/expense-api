<?php

declare(strict_types=1);

namespace App\Enum;

enum ExpenseType: int
{
    case Entertainment = 1;
    case Food = 2;
    case Transportation = 3 ;
    case Other = 4;
}
