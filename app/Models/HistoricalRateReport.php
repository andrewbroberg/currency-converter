<?php

namespace App\Models;

use App\Casts\CurrencyCodeCast;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoricalRateReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'immutable_date',
        'type' => ReportType::class,
        'status' => ReportStatus::class,
        'source' => CurrencyCodeCast::class,
        'currency' => CurrencyCodeCast::class,
    ];

    public function conversions(): HasMany
    {
        return $this->hasMany(HistoricalRateReportConversion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
