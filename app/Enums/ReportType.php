<?php

namespace App\Enums;

enum ReportType: string
{
    case ANNUAL = 'annual';
    case SEMIANNUAL = 'semiannual';
    case MONTHLY = 'monthly';
}
