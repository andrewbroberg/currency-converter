<?php

namespace App\Services;

use App\ValueObjects\CurrencyCode;
use App\Contracts\CurrencyConverter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use App\Exceptions\FailedToConvertCurrencies;
use App\ValueObjects\CurrencyConversion;

class CurrencyLayerConverter implements CurrencyConverter
{
    public function __construct(
        private readonly string $apiKey
    ) {
    }

    /**
     * @inheritDoc
     */
    public function liveConversion(CurrencyCode $source, array $currencies): array
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get("https://api.apilayer.com/currency_data/live", [
            'source' => $source->code,
            'currencies' => Collection::make($currencies)->map(fn(CurrencyCode $code) => $code->code)->implode(','),
        ]);

        if (!$response->successful()) {
            throw new FailedToConvertCurrencies();
        }

        $returnedSource = CurrencyCode::fromString($response->json('source'));

        return Collection::make($response->json('quotes'))
            ->map(function (float $rate, string $code) use ($returnedSource) {
                return new CurrencyConversion(
                    $returnedSource,
                    CurrencyCode::fromString(substr($code, 3)),
                    $rate,
                );
            })->values()
            ->all();
    }
}
