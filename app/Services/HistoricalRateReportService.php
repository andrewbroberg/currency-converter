<?php

namespace App\Services;

use App\Enums\ReportType;
use DateTimeInterface;
use App\Models\HistoricalRateReport;
use App\Jobs\ProcessHistoricalRateReport;
use App\Models\User;
use App\Enums\ReportStatus;
use App\ValueObjects\CurrencyCode;

class HistoricalRateReportService
{
    public function create(
        DateTimeInterface $date,
        ReportType $reportType,
        CurrencyCode $source,
        CurrencyCode $conversionCurrency,
        User $user
    ): HistoricalRateReport {
        $report = $user->historicalReports()->create([
            'type' => $reportType,
            'date' => $date,
            'source' => $source,
            'currency' => $conversionCurrency,
            'status' => ReportStatus::PENDING,
        ]);

        ProcessHistoricalRateReport::dispatch($report);

        return $report;
    }
}
