<?php

use App\Http\Controllers\ConversionRatesController;
use App\Http\Controllers\HistoricalRateReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('live-currency-conversion', ConversionRatesController::class)->name('live-currency-conversion');
    Route::post('historical-rate-reports', [HistoricalRateReportController::class, 'store'])->name('historical-rates-report.store');
    Route::get('historical-rate-reports', [HistoricalRateReportController::class, 'index'])->name('historical-rates-report.index');
});
