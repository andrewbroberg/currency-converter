<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ReportType;
use App\Rules\CurrencyCode;

class CreateHistoricalRatesReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before:today'],
            'reportType' => ['required', new Enum(ReportType::class)],
            'source' => ['required', new CurrencyCode()],
            'currency' => ['required', new CurrencyCode(), 'different:source']
        ];
    }
}
