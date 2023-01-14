<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ReportType;
use App\Models\User;
use App\ValueObjects\CurrencyCode;

class HistoricalRateReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source' => CurrencyCode::fromString('USD'),
            'currency' => CurrencyCode::fromString('AUD'),
            'type' => ReportType::ANNUAL,
            'date' => $this->faker->date()
        ];
    }
}
