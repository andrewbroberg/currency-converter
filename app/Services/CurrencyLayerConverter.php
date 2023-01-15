<?php

namespace App\Services;

use App\Contracts\CurrencyConverter;
use App\Exceptions\FailedToConvertCurrencies;
use App\ValueObjects\CurrencyCode;
use App\ValueObjects\CurrencyConversion;
use App\ValueObjects\CurrencyConversionForDate;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CurrencyLayerConverter implements CurrencyConverter
{
    public function __construct(
        private readonly string $apiKey
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function liveConversion(CurrencyCode $source, array $currencies): array
    {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get('https://api.apilayer.com/currency_data/live', [
            'source' => $source->code,
            'currencies' => Collection::make($currencies)->map(fn (CurrencyCode $code) => $code->code)->implode(','),
        ]);

        if (! $response->successful()) {
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

    /** {@inheritDoc} */
    public function historicalRates(
        CurrencyCode $source,
        CurrencyCode $currency,
        DateTimeInterface $fromDate,
        DateTimeInterface $toDate
    ): array {
        $response = Http::withHeaders([
            'apiKey' => $this->apiKey,
        ])->get('https://api.apilayer.com/currency_data/timeframe', [
            'source' => $source->code,
            'currencies' => $currency->code,
            'start_date' => $fromDate->format('Y-m-d'),
            'end_date' => $toDate->format('Y-m-d'),
        ]);

        if (! $response->successful()) {
            throw new FailedToConvertCurrencies();
        }

        $source = CurrencyCode::fromString($response->json('source'));

        return Collection::make($response->json('quotes'))->map(function ($quote, $date) use ($source) {
            return new CurrencyConversionForDate(
                new CurrencyConversion(
                    $source,
                    CurrencyCode::fromString(substr(key($quote), 3)),
                    $quote[key($quote)],
                ),
                new DateTimeImmutable($date)
            );
        })->values()->toArray();
    }
}
