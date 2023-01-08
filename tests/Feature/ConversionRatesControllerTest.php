<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Contracts\CurrencyConverter;
use App\ValueObjects\CurrencyConversion;
use App\ValueObjects\CurrencyCode;

class ConversionRatesControllerTest extends TestCase
{
    /** @test */
    public function it_returns_current_conversion_rates_for_up_to_5_currencies(): void
    {
        $this->mockCurrencyConverter();

        $this->be(User::factory()->create())
            ->getJson(route('live-currency-conversion', [
                'source' => 'USD',
                'currencies' => ['AUD', 'EUR', 'CAD'],
            ]))
            ->assertOk()
            ->assertExactJson([
                'source' => 'USD',
                'conversions' => [
                    'AUD' => 0.678,
                    'EUR' => 1.337,
                    'CAD' => 1.345,
                ],
            ]);
    }

    /**
     * @test
     * @dataProvider validationData
     */
    public function it_validates_the_request(array $payload, array $errors): void
    {
        $this->be(User::factory()->create())
            ->getJson(route('live-currency-conversion', $payload))
            ->assertUnprocessable()
            ->assertJsonValidationErrors($errors);
    }

    /** @test */
    public function validationData(): array
    {
        return [
            'Required fields' => [
                [],
                [
                    'source' => 'The source field is required.',
                    'currencies' => 'The currencies field is required.',
                ],
            ],
            'Source must be a max of 3 Uppercase characters' => [
                [
                    'source' => 'TEST',
                ],
                [
                    'source' => 'The source is not a valid currency code',
                ],
            ],
            'Currencies must be an array' => [
                [
                    'currencies' => 'not-an-array',
                ],
                [
                    'currencies' => 'The currencies must be an array.',
                ],
            ],
            'Currencies must be an array of distinct values' => [
                [
                    'currencies' => ['AUD', 'AUD'],
                ],
                [
                    'currencies.0' => 'The currencies.0 field has a duplicate value.',
                    'currencies.1' => 'The currencies.1 field has a duplicate value.',
                ],
            ],
            'Currencies must be 3 uppercase characters' => [
                [
                    'currencies' => ['AUDD'],
                ],
                [
                    'currencies.0' => 'The currencies.0 is not a valid currency code',
                ],
            ],
            'Currencies must have a max of 5 items' => [
                [
                    'currencies' => ['USD', 'AUD', 'CAD', 'EUR', 'TES', 'BAR'],
                ],
                [
                    'currencies' => 'The currencies must not have more than 5 items.',
                ],
            ],
        ];
    }

    private function mockCurrencyConverter(): void
    {
        $this->mock(CurrencyConverter::class)
            ->expects('liveConversion')
            ->andReturn([
                new CurrencyConversion(
                    CurrencyCode::fromString('USD'),
                    CurrencyCode::fromString('AUD'),
                    0.678
                ),
                new CurrencyConversion(
                    CurrencyCode::fromString('USD'),
                    CurrencyCode::fromString('EUR'),
                    1.337
                ),
                new CurrencyConversion(
                    CurrencyCode::fromString('USD'),
                    CurrencyCode::fromString('CAD'),
                    1.345
                ),
            ]);
    }
}
