<?php

namespace Tests\Feature\Console\Commands;


use Tests\TestCase;
use App\Models\HistoricalRateReport;
use App\Console\Commands\ProcessPendingHistoricalRateReports;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessHistoricalRateReport;
use App\Enums\ReportStatus;

class ProcessPendingHistoricalRateReportsTest extends TestCase
{
    /** @test */
    public function it_dispatches_job_for_each_pending_report(): void
    {
        Bus::fake();

        HistoricalRateReport::factory()
            ->count(2)
            ->create();

        $this->artisan(ProcessPendingHistoricalRateReports::class)
            ->assertOk();

        Bus::assertDispatchedTimes(ProcessHistoricalRateReport::class, 2);
    }

    /** @test */
    public function it_doesnt_dispatch_job_for_already_completed_report(): void
    {
        Bus::fake();

        HistoricalRateReport::factory()
            ->create([
                'status' => ReportStatus::COMPLETED
            ]);

        $this->artisan(ProcessPendingHistoricalRateReports::class)
            ->assertOk();

        Bus::assertNothingDispatched();
    }
}
