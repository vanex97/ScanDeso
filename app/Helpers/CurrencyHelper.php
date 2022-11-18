<?php

namespace App\Helpers;

class CurrencyHelper
{
    public const NANOS_IN_DESO = 1000000000;
    public const CENTS_IN_DOLLAR = 100;

    public static function nanoToDeso($amount, $decimals = 2)
    {
        if ($decimals === null) {
            return sprintf('%f', $amount / self::NANOS_IN_DESO);
        }

        return number_format($amount / self::NANOS_IN_DESO, $decimals);
    }

    public static function centsToDollars($amount, $decimals = 2)
    {
        return number_format($amount / self::CENTS_IN_DOLLAR, $decimals);
    }

    public static function nanoToDollars($nanoAmount, $desoPrice, $decimals = 2)
    {
        $desoAmount = $nanoAmount / self::NANOS_IN_DESO;

        return number_format($desoAmount * $desoPrice, $decimals);
    }

    public static function hexdecToDecimal($hexdec)
    {
        return number_format(hexdec($hexdec) / pow(10, 18));
    }
}
