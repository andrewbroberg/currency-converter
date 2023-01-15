<?php

namespace App\Http\Resources;

use App\Models\HistoricalRateReportConversion;
use Illuminate\Http\Resources\Json\JsonResource;

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
