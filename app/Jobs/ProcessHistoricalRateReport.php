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
use DatePeriod;
use Carbon\CarbonImmutable;
use DateInterval;
use App\Enums\ReportStatus;
use Illuminate\Support\Collection;
use App\ValueObjects\CurrencyConversionForDate;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

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

        $dateRange = $this->determineDateRanges($this->report->date, $this->report->type);

        $conversionRates = $currencyConverter->historicalRates(
            $this->report->source,
            $this->report->currency,
            $dateRange->getStartDate(),
            $dateRange->getEndDate()
        );

        $conversionRates = $this->rejectNonRequiredDates($conversionRates, $dateRange);

        DB::transaction(function () use ($conversionRates) {
            $this->report->conversions()->createMany($conversionRates->map(function (CurrencyConversionForDate $conversion) {
                return [
                    'date' => $conversion->date,
                    'source' => $conversion->conversion->source,
                    'currency' => $conversion->conversion->currency,
                    'conversion_rate' => $conversion->conversion->conversionRate,
                ];
            }));

            $this->report->update(['status' => ReportStatus::COMPLETED]);
        });
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

    /**
     * @param array $conversionRates
     * @param DatePeriod $datePeriod
     * @return Collection<CurrencyConversionForDate>
     */
    private function rejectNonRequiredDates(array $conversionRates, DatePeriod $datePeriod): Collection
    {
        $dates = CarbonPeriod::between($datePeriod->getStartDate(), $datePeriod->getEndDate())
            ->interval($this->report->type->interval())
            ->settings(['monthOverflow' => false])
            ->toArray();

        return Collection::make($conversionRates)
            ->filter(fn (CurrencyConversionForDate $conversion) => in_array($conversion->date, $dates));
    }

    public function uniqueId(): int
    {
        return $this->report->id;
    }
}
