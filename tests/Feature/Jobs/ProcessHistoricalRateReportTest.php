<?php

namespace Tests\Feature\Jobs;


use Tests\TestCase;
use App\Models\HistoricalRateReport;
use App\Models\User;
use App\Enums\ReportType;
use App\ValueObjects\CurrencyCode;
use App\Enums\ReportStatus;
use App\Jobs\ProcessHistoricalRateReport;
use App\Models\HistoricalRateReportConversion;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use App\Contracts\CurrencyConverter;
use Hamcrest\Matchers;
use DateTime;
use Carbon\Carbon;
use App\ValueObjects\CurrencyConversionForDate;
use App\ValueObjects\CurrencyConversion;

class ProcessHistoricalRateReportTest extends TestCase
{
    /** @test */
    public function it_can_process_an_annual_report_request(): void
    {
        $usd = CurrencyCode::fromString('USD');
        $aud = CurrencyCode::fromString('AUD');
        $report = HistoricalRateReport::factory()
            ->for(User::factory())
            ->create([
                'type' => ReportType::ANNUAL,
                'source' => $usd,
                'currency' => $aud,
                'date' => '2022-12-01',
                'status' => ReportStatus::PENDING
            ]);

        $datesToReturnFromCurrencyConverter = CarbonPeriod::between('2021-12-01', '2022-12-01')->interval(CarbonInterval::day())->toArray();
        $expectedDates = CarbonPeriod::between('2021-12-01', '2022-12-01')->interval(CarbonInterval::month());

        $return = array_map(fn (Carbon $date) =>
            new CurrencyConversionForDate(
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
                Matchers::equalTo(new DateTime('2021-12-01')),
                Matchers::equalTo(new DateTime('2022-12-01'))
            )->andReturn($return);

        ProcessHistoricalRateReport::dispatch($report);

        $this->assertDatabaseHas(HistoricalRateReport::class, [
            'id' => $report->id,
            'status' => ReportStatus::COMPLETED
        ]);

        $expectedDates->forEach(fn (Carbon $date) =>
            $this->assertDatabaseHas(HistoricalRateReportConversion::class, [
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
            'status' => ReportStatus::COMPLETED
        ]);

        $this->mock(CurrencyConverter::class)
            ->expects('historicalRates')
            ->never();

        ProcessHistoricalRateReport::dispatch($report);
    }
}
