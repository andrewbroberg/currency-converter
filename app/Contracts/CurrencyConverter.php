<?php

namespace App\Contracts;

use App\ValueObjects\CurrencyCode;
use App\ValueObjects\CurrencyConversion;

interface CurrencyConverter
{
    /**
     * @param CurrencyCode $source
     * @param CurrencyCode[] $currencies
     * @return CurrencyConversion[]
     */
    public function liveConversion(CurrencyCode $source, array $currencies): array;
}
