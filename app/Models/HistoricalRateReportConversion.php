<?php

namespace App\Models;

use App\Casts\CurrencyCodeCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
