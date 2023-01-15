<?php

namespace Tests\Feature\Jobs;

use App\Contracts\CurrencyConverter;
use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Jobs\ProcessHistoricalRateReport;
use App\Models\HistoricalRateReport;
use App\Models\HistoricalRateReportConversion;
use App\Models\User;
use App\ValueObjects\CurrencyCode;
use App\ValueObjects\CurrencyConversion;
use App\ValueObjects\CurrencyConversionForDate;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTime;
use Generator;
use Hamcrest\Matchers;
use Tests\TestCase;

class ProcessHistoricalRateReportTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider reportTypes
     */
    public function it_can_process_an_reports_for_each_type(ReportType $reportType, string $fromDate, string $toDate, CarbonInterval $interval): void
    {
        $usd = CurrencyCode::fromString('USD');
        $aud = CurrencyCode::fromString('AUD');
        $report = HistoricalRateReport::factory()
            ->for(User::factory())
            ->create([
                'type' => $reportType,
                'source' => $usd,
                'currency' => $aud,
                'date' => $toDate,
                'status' => ReportStatus::PENDING,
            ]);

        $datesToReturnFromCurrencyConverter = CarbonPeriod::between($fromDate, $toDate)->interval(CarbonInterval::day())->toArray();
        $expectedDates = CarbonPeriod::between($fromDate, $toDate)->interval($interval);

        $return = array_map(fn (Carbon $date) => new CurrencyConversionForDate(
                new CurrencyConversion(
                    $usd,
                    $aud,
                    1.337,
                ),
                $date,
            ), $datesToReturnFromCurrencyConverter);

        $this->mock(CurrencyConverter::class)
            ->expects('historicalRates')
            ->with(
                Matchers::equalTo($usd),
                Matchers::equalTo($aud),
                Matchers::equalTo(new DateTime($fromDate)),
                Matchers::equalTo(new DateTime($toDate))
            )->andReturn($return);

        ProcessHistoricalRateReport::dispatch($report);

        $this->assertDatabaseHas(HistoricalRateReport::class, [
            'id' => $report->id,
            'status' => ReportStatus::COMPLETED,
        ]);

        $this->assertDatabaseCount(HistoricalRateReportConversion::class, $expectedDates->count());

        $expectedDates->forEach(fn (Carbon $date) => $this->assertDatabaseHas(HistoricalRateReportConversion::class, [
            'historical_rate_report_id' => $report->id,
            'source' => $report->source->code,
            'currency' => $report->currency->code,
            'conversion_rate' => 1.337,
            'date' => $date->format('Y-m-d H:i:s'),
        ])
        );
    }

    /** @test */
    public function it_doesnt_reprocess_a_completed_report(): void
    {
        $report = HistoricalRateReport::factory()->create([
            'status' => ReportStatus::COMPLETED,
        ]);

        $this->mock(CurrencyConverter::class)
            ->expects('historicalRates')
            ->never();

        ProcessHistoricalRateReport::dispatch($report);
    }

    public function reportTypes(): Generator
    {
        yield 'Annual' => [
            ReportType::ANNUAL,
            '2021-12-31',
            '2022-12-31',
            CarbonInterval::month(),
        ];

        yield 'Semi-Annual' => [
            ReportType::SEMIANNUAL,
            '2022-06-01',
            '2022-12-01',
            CarbonInterval::week(),
        ];

        yield 'Month' => [
            ReportType::MONTHLY,
            '2022-11-01',
            '2022-12-01',
            CarbonInterval::day(),
        ];
    }
}
