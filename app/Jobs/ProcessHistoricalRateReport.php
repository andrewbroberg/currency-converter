<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\HistoricalRateReport;
use App\Contracts\CurrencyConverter;
use App\Enums\ReportType;
use DateTimeInterface;
use DatePeriod;
use Carbon\CarbonImmutable;
use DateInterval;
use App\Enums\ReportStatus;
use Illuminate\Support\Collection;

class ProcessHistoricalRateReport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private HistoricalRateReport $report)
    {
    }

    public function handle(CurrencyConverter $currencyConverter): void
    {
        if ($this->report->status === ReportStatus::COMPLETED) {
            return;
        }

        // Determine the date range based on the report type
        $dateRange = $this->determineDateRanges($this->report->date, $this->report->type);
        // Retrieve historical rates for the date range
        $conversionRates = $currencyConverter->historicalRates(
            $this->report->source,
            $this->report->currency,
            $dateRange->getStartDate(),
            $dateRange->getEndDate()
        );
        // Reject dates not required
        $conversionRates = $this->rejectNonRequiredDates($conversionRates);
        // Store dates against the report
        // Update the report to be completed
    }

    private function determineDateRanges(CarbonImmutable $date, ReportType $reportType): DatePeriod {
        $startDate = match ($reportType) {
            ReportType::ANNUAL => $date->subMonths(12),
            ReportType::SEMIANNUAL => $date->subMonths(6),
            ReportType::MONTHLY => $date->subMonth()
        };

        return new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $date,
        );
    }

    public function uniqueBy(): int
    {
        return $this->report->id;
    }
}
