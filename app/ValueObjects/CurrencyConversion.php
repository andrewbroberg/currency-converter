<?php

namespace App\ValueObjects;

final class CurrencyConversion
{
    public function __construct(
        public readonly CurrencyCode $source,
        public readonly CurrencyCode $currency,
        public readonly float $conversionRate,
    )
    {
    }

}
