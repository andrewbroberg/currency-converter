<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHistoricalRatesReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d', 'before:today'],
            'reportType' => ['required', 'in:annual,semiannual,monthly'],
        ];
    }
}
