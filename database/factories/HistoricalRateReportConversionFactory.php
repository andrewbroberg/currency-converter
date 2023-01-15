<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\HistoricalRateReport;
use App\ValueObjects\CurrencyCode;

class HistoricalRateReportConversionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'historical_rate_report_id' => HistoricalRateReport::factory(),
            'date' => Carbon::now(),
            'source' => CurrencyCode::fromString($this->faker->toUpper($this->faker->lexify('???'))),
            'currency' => CurrencyCode::fromString($this->faker->toUpper($this->faker->lexify('???'))),
            'conversion_rate' => $this->faker->randomFloat(),
        ];
    }
}
