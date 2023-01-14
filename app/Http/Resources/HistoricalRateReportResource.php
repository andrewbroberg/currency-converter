<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\HistoricalRateReport;
use App\Models\HistoricalRateReportConversion;

/**
 * @mixin HistoricalRateReport
 */
class HistoricalRateReportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'source' => $this->source->code,
            'currency' => $this->currency->code,
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'conversions' => $this->whenLoaded('conversions', fn() => HistoricalRateReportConversionResource::collection($this->conversions)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
