<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversionRatesController;
use Tests\Featre\Http\Controllers\HistoricalRatesReportControllerTest;
use App\Http\Controllers\HistoricalRatesReportController;

Route::middleware('auth:sanctum')->group(function () {
   Route::get('/user', function (Request $request) {
       return $request->user();
   });

   Route::get('live-currency-conversion', ConversionRatesController::class)->name('live-currency-conversion');
   Route::post('historical-rates-report', [HistoricalRatesReportController::class, 'store'])->name('historical-rates-report.store');
});
