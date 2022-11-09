<?php

namespace App\Helpers;

class StringHelper
{
    public static function formatTransactionType($type)
    {
        $type = str_replace('_', ' ', $type);

        return ucwords(strtolower($type));
    }
}
