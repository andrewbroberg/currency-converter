<?php

namespace Tests\Featre\Http\Controllers;

use App\Models\User;
use Tests\TestCase;
use Carbon\Carbon;

class HistoricalRatesReportControllerTest extends TestCase
{
    /**
     * @test
     * @dataProvider validationData
     */
    public function it_validates_the_request_to_create_a_report(array $payload, array $errors): void
    {
        Carbon::setTestNow('2023-01-01');
        $this->be(User::factory()->create())
            ->postJson(route('historical-rates-report.store', $payload))
            ->assertUnprocessable()
            ->assertJsonValidationErrors($errors);
    }

    public function validationData(): array
    {
        return [
            'Required fields' => [
                [],
                [
                    'date' => 'The date field is required.',
                    'reportType' => 'The report type field is required.',
                ],
            ],
            'Date field must be a valid date' => [
                ['date' => 1234],
                [
                    'date' => 'The date does not match the format Y-m-d.'
                ]
            ],
            'Date must be in the past' => [
                ['date' => '2023-08-01'],
                [
                    'date' => 'The date must be a date before today.'
                ]
            ],
            'Report type must be a valid type' => [
                ['reportType' => 'not-valid'],
                [
                    'reportType' => 'The selected report type is invalid.'
                ]
            ]
        ];
    }

}
