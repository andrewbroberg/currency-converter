<?php

namespace App\Http\Controllers;

use App\Http\Requests\LiveCurrencyConversionRequest;
use App\ValueObjects\CurrencyCode;
use App\Contracts\CurrencyConverter;
use Illuminate\Support\Collection;
use App\ValueObjects\CurrencyConversion;

class ConversionRatesController extends Controller
{
    public function __invoke(
        LiveCurrencyConversionRequest $request,
        CurrencyConverter $currencyConverter
    ) {
        $source = CurrencyCode::fromString($request->validated('source'));
        $currencies = $request->currencies();

        $converted = $currencyConverter->liveConversion($source, $currencies);

        return response()->json([
            'source' => $source->code,
            'conversions' => Collection::make($converted)
                ->mapWithKeys(function (CurrencyConversion $conversion) {
                    return [$conversion->currencyCode->code => $conversion->conversionRate];
                })->all(),
        ]);
    }
}
