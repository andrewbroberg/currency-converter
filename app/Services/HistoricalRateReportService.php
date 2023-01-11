<?php

namespace App\Services;

use App\Enums\ReportType;
use DateTimeInterface;
use App\Models\HistoricalRateReport;
use App\Jobs\ProcessHistoricalRateReport;
use App\Models\User;
use App\Enums\ReportStatus;

class HistoricalRateReportService
{
    public function create(
        DateTimeInterface $date,
        ReportType $reportType,
        User $user
    ): HistoricalRateReport {
        $report = $user->historicalReports()->create([
            'type' => $reportType,
            'date' => $date,
            'status' => ReportStatus::PENDING,
        ]);

        ProcessHistoricalRateReport::dispatch($report);

        return $report;
    }
}
