<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ReportType;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoricalRateReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'immutable_date',
        'type' => ReportType::class,
        'status' => ReportStatus::class,
    ];

    public function conversions(): HasMany
    {
        return $this->hasMany(HistoricalRateReportConversion::class);
    }
}
