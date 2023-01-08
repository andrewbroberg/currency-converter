<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConversionRatesController;

Route::middleware('auth:sanctum')->group(function () {
   Route::get('/user', function (Request $request) {
       return $request->user();
   });

   Route::get('live-currency-conversion', ConversionRatesController::class)->name('live-currency-conversion');
});
