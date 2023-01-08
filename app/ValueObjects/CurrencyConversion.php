<?php

namespace App\ValueObjects;

final class CurrencyConversion
{
    public function __construct(
        public readonly CurrencyCode $source,
        public readonly CurrencyCode $currencyCode,
        public readonly float $conversionRate,
    )
    {
    }

}
