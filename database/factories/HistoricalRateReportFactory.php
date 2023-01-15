<?php

namespace Database\Factories;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Models\User;
use App\ValueObjects\CurrencyCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class HistoricalRateReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source' => CurrencyCode::fromString('USD'),
            'currency' => CurrencyCode::fromString('AUD'),
            'status' => ReportStatus::PENDING,
            'type' => ReportType::ANNUAL,
            'date' => $this->faker->date(),
        ];
    }
}
