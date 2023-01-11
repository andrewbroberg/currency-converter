<?php

namespace App\ValueObjects;

use DateTimeInterface;

class CurrencyConversionForDate
{
    public function __construct(
        public readonly CurrencyConversion $conversion,
        public readonly DateTimeInterface $date
    ) {
    }
}
