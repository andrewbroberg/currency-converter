<?php

namespace App\Contracts;

use App\ValueObjects\CurrencyCode;
use App\ValueObjects\CurrencyConversion;
use App\ValueObjects\CurrencyConversionForDate;
use DateTimeInterface;

interface CurrencyConverter
{
    /**
     * @param  CurrencyCode  $source
     * @param  CurrencyCode[]  $currencies
     * @return CurrencyConversion[]
     */
    public function liveConversion(CurrencyCode $source, array $currencies): array;

    /**
     * @return CurrencyConversionForDate[]
     */
    public function historicalRates(CurrencyCode $source, CurrencyCode $currency, DateTimeInterface $fromDate, DateTimeInterface $toDate): array;
}
