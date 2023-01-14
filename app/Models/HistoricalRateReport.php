<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReportType;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\ValueObjects\CurrencyCode;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\CurrencyCodeCast;

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
