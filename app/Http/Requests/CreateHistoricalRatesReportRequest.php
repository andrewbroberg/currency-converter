<?php

namespace App\Http\Requests;

use App\Enums\ReportType;
use App\Rules\CurrencyCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateHistoricalRatesReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before:today'],
            'reportType' => ['required', new Enum(ReportType::class)],
            'source' => ['required', new CurrencyCode()],
            'currency' => ['required', new CurrencyCode(), 'different:source'],
        ];
    }
}
