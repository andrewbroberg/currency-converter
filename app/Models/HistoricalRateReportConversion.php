<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\ValueObjects\CurrencyCode;
use App\Casts\CurrencyCodeCast;

class HistoricalRateReportConversion extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'source' => CurrencyCodeCast::class,
        'currency' => CurrencyCodeCast::class,
        'conversion_rate' => 'float',
    ];
}
