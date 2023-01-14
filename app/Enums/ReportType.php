<?php

namespace App\Enums;

use DateInterval;

enum ReportType: string
{
    case ANNUAL = 'annual';
    case SEMIANNUAL = 'semiannual';
    case MONTHLY = 'monthly';

    public function interval(): DateInterval
    {
        return match ($this) {
            self::ANNUAL => new DateInterval('P1M'),
            self::SEMIANNUAL => new DateInterval('P1W'),
            self::MONTHLY => new DateInterval('P1D'),
        };
    }
}
