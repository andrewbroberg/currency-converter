<?php

namespace App\Console\Commands;

use App\Enums\ReportStatus;
use App\Jobs\ProcessHistoricalRateReport;
use App\Models\HistoricalRateReport;
use Illuminate\Console\Command;

class ProcessPendingHistoricalRateReports extends Command
{
    protected $signature = 'historical-rate-reports:process';

    protected $description = 'Process any pending historical rate reports';

    public function handle(): int
    {
        HistoricalRateReport::query()
            ->whereStatus(ReportStatus::PENDING)
            ->each(fn (HistoricalRateReport $report) => ProcessHistoricalRateReport::dispatch($report));

        return self::SUCCESS;
    }
}
