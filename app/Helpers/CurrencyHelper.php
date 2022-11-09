<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function nanoToDeso($amount, $decimals = 2)
    {
        if ($decimals === null) {
            return sprintf('%f', $amount / 1000000000);
        }

        return number_format($amount / 1000000000, $decimals);
    }

    public static function centsToDollars($amount, $decimals = 2)
    {
        return number_format($amount / 100, $decimals);
    }

    public static function nanoToDollars($nanoAmount, $desoPrice, $decimals = 2)
    {
        // TODO затычка
        $desoAmount = $nanoAmount / 1000000000;
//        $desoAmount = self::nanoToDeso($nanoAmount);

        return number_format($desoAmount * $desoPrice, $decimals);
    }
}
