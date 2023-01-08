<?php

namespace Tests\Feature\Services;

use App\Services\CurrencyLayerConverter;
use App\ValueObjects\CurrencyCode;
use Illuminate\Support\Facades\Http;
use App\ValueObjects\CurrencyConversion;
use Tests\TestCase;

class CurrencyLayerConverterTest extends TestCase
{
    /** @test */
    public function it_can_request_live_conversion_rates_for_currencies(): void
    {
        Http::preventStrayRequests();

        Http::fake([
            'https://api.apilayer.com/currency_data/live*' => Http::response([
                'quotes' => [
                    'AUDCAD' => 0.925443,
                    'AUDEUR' => 0.645254,
                    'AUDUSD' => 0.688087,
                ],
                'source' => 'AUD',
                'success' => true,
                'timestamp' => 1337
            ])
        ]);

        $currencyConverter = new CurrencyLayerConverter('test-api-key');
        $aud = CurrencyCode::fromString('AUD');
        $cad = CurrencyCode::fromString('CAD');
        $eur = CurrencyCode::fromString('EUR');
        $usd = CurrencyCode::fromString('USD');

        $conversions = $currencyConverter->liveConversion(
            $aud,
            [
                $cad,
                $eur,
                $usd,
            ]
        );

        self::assertEquals([
            new CurrencyConversion($aud, $cad, 0.925443),
            new CurrencyConversion($aud, $eur, 0.645254),
            new CurrencyConversion($aud, $usd, 0.688087),
        ], $conversions);
    }
}
