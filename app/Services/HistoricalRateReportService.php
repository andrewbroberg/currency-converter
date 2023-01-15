<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Jobs\ProcessHistoricalRateReport;
use App\Models\HistoricalRateReport;
use App\Models\User;
use App\ValueObjects\CurrencyCode;
use DateTimeInterface;
use Illuminate\Support\Collection;

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

    /**
     * @param  User  $user
     * @return Collection<HistoricalRateReport>
     */
    public function listForUser(User $user): Collection
    {
        return $user->historicalReports()->latest()->get();
    }
}
