<?php

namespace App\Http\Controllers;

use App\Contracts\CurrencyConverter;
use App\Http\Requests\LiveCurrencyConversionRequest;
use App\ValueObjects\CurrencyCode;
use App\ValueObjects\CurrencyConversion;
use Illuminate\Support\Collection;

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
                    return [$conversion->currency->code => $conversion->conversionRate];
                })->all(),
        ]);
    }
}
