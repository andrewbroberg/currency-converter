<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateHistoricalRatesReportRequest;
use App\Services\HistoricalRateReportService;
use App\Enums\ReportType;
use App\Http\Resources\HistoricalRateReportResource;
use App\ValueObjects\CurrencyCode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\HistoricalRateReport;
use Inertia\Inertia;
use Inertia\Response;

class HistoricalRateReportController extends Controller
{
    public function store(
        CreateHistoricalRatesReportRequest $request,
        HistoricalRateReportService $historicalRateReportService
    ): HistoricalRateReportResource {
        $report = $historicalRateReportService->create(
            $request->date('date'),
            $request->enum('reportType', ReportType::class),
            CurrencyCode::fromString($request->validated('source')),
            CurrencyCode::fromString($request->validated('currency')),
            $request->user()
        );

        return new HistoricalRateReportResource($report);
    }

    public function index(Request $request, HistoricalRateReportService $historicalRateReportService): AnonymousResourceCollection
    {
        return HistoricalRateReportResource::collection(
            $historicalRateReportService->listForUser($request->user())
        );
    }

    public function show(HistoricalRateReport $report): Response
    {
        return Inertia::render('ViewHistoricalReport', [
            'report' => $report->loadMissing('conversions')
        ]);
    }
}
