<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHistoricalRatesReportRequest;
use App\Services\HistoricalRateReportService;
use App\Enums\ReportType;
use App\Http\Resources\HistoricalRateReportResource;

class HistoricalRateReportController extends Controller
{
    public function store(
        CreateHistoricalRatesReportRequest $request,
        HistoricalRateReportService $historicalRateReportService
    ): HistoricalRateReportResource {
        $report = $historicalRateReportService->create(
            $request->date('date'),
            $request->enum('reportType', ReportType::class),
            $request->user()
        );

        return new HistoricalRateReportResource($report);
    }
}
