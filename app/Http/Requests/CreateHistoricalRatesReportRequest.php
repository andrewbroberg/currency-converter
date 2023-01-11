<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ReportType;

class CreateHistoricalRatesReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d', 'before:today'],
            'reportType' => ['required', new Enum(ReportType::class)],
        ];
    }
}
