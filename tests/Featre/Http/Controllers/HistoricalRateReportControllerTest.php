<?php

namespace Tests\Featre\Http\Controllers;

use App\Models\User;
use Tests\TestCase;
use Carbon\Carbon;
use App\Enums\ReportType;
use App\Models\HistoricalRateReport;
use App\Enums\ReportStatus;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessHistoricalRateReport;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia;

class HistoricalRateReportControllerTest extends TestCase
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

    /** @test */
    public function it_persists_the_requested_report_to_the_database(): void
    {
        Carbon::setTestNow('2023-01-01');
        Bus::fake();

        $user = User::factory()->create();
        $this->be($user)
            ->postJson(route('historical-rates-report.store', [
                'date' => '2022-12-01',
                'reportType' => ReportType::ANNUAL,
                'source' => 'USD',
                'currency' => 'AUD',
            ]))
            ->assertCreated();

        $this->assertDatabaseHas(HistoricalRateReport::class, [
            'user_id' => $user->id,
            'type' => ReportType::ANNUAL,
            'date' => '2022-12-01 00:00:00',
            'status' => ReportStatus::PENDING,
            'source' => 'USD',
            'currency' => 'AUD',
        ]);
    }

    /** @test */
    public function it_can_return_a_list_of_historical_reports_with_conversions(): void
    {
        $user = User::factory()->create();
        [$report1,] = HistoricalRateReport::factory()
            ->count(2)
            ->for($user)
            ->create([
                'status' => ReportStatus::COMPLETED,
            ]);

        $this->be($user)
            ->getJson(route('historical-rates-report.index'))
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->has('data', 2)
                ->has('data.0', fn(AssertableJson $json) => $json->whereAll([
                    'id' => $report1->id,
                    'status' => ReportStatus::COMPLETED->value,
                    'type' => $report1->type->value,
                    'date' => $report1->date->format('Y-m-d'),
                    'source' => 'USD',
                    'currency' => 'AUD',
                    'created_at' => $report1->created_at->toIso8601ZuluString('microseconds'),
                    'updated_at' => $report1->created_at->toIso8601ZuluString('microseconds'),
                ])
                )
            );
    }


    /** @test */
    public function it_dispatches_the_report_to_be_processed(): void
    {
        Carbon::setTestNow('2023-01-01');
        Bus::fake();

        $user = User::factory()->create();
        $this->be($user)
            ->postJson(route('historical-rates-report.store', [
                'date' => '2022-12-01',
                'reportType' => ReportType::ANNUAL,
                'source' => 'USD',
                'currency' => 'AUD',
            ]))
            ->assertCreated();

        Bus::assertDispatched(ProcessHistoricalRateReport::class);
    }

    public function validationData(): array
    {
        return [
            'Required fields' => [
                [],
                [
                    'date' => 'The date field is required.',
                    'reportType' => 'The report type field is required.',
                    'source' => 'The source field is required.',
                    'currency' => 'The currency field is required.',
                ],
            ],
            'Date field must be a valid date' => [
                ['date' => 1234],
                [
                    'date' => 'The date is not a valid date.',
                ],
            ],
            'Date must be in the past' => [
                ['date' => '2023-08-01'],
                [
                    'date' => 'The date must be a date before today.',
                ],
            ],
            'Report type must be a valid type' => [
                ['reportType' => 'not-valid'],
                [
                    'reportType' => 'The selected report type is invalid.',
                ],
            ],
            'Source and currency must not be the same' => [
                [
                    'source' => 'USD',
                    'currency' => 'USD',
                ],
                [
                    'currency' => 'The currency and source must be different.',
                ]
            ]
        ];
    }

    /** @test */
    public function a_user_can_view_a_report(): void
    {
        $user = User::factory()->create();

        $report = HistoricalRateReport::factory()
            ->for($user)
            ->hasConversions(2)
            ->create();

        $this->be($user)
            ->get(route('historical-rate-report.show', $report->id))
            ->assertOk()
            ->assertInertia(fn(AssertableInertia $page) => $page
                ->component('ViewHistoricalReport')
                ->has('report', fn(AssertableInertia $page) => $page
                    ->where('id', $report->id)
                    ->etc()
                )
            );
    }

    /** @test */
    public function a_user_cannot_view_another_users_report(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $report = HistoricalRateReport::factory()
            ->for($otherUser)
            ->hasConversions(2)
            ->create();

        $this->be($user)
            ->get(route('historical-rate-report.show', $report->id))
            ->assertForbidden();
    }
}
