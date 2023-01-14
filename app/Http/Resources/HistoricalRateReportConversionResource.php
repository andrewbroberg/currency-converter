<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\HistoricalRateReportConversion;

/**
 * @mixin HistoricalRateReportConversion
 */
class HistoricalRateReportConversionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'historical_rate_report_id' => $this->historical_rate_report_id,
            'source' => $this->source->code,
            'currency' => $this->currency->code,
            'rate' => $this->rate,
        ];
    }
}
