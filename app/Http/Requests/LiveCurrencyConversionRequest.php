<?php

namespace App\Http\Requests;

use App\Rules\CurrencyCode as CurrencyCodeRule;
use App\ValueObjects\CurrencyCode;
use Illuminate\Foundation\Http\FormRequest;

class LiveCurrencyConversionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'source' => ['required', 'string', new CurrencyCodeRule()],
            'currencies' => ['required', 'array', 'max:5'],
            'currencies.*' => ['required', 'string', 'distinct', new CurrencyCodeRule()],
        ];
    }

    /**
     * @return CurrencyCode[]
     */
    public function currencies(): array
    {
        return $this
            ->collect('currencies')
            ->map(function (string $currency): CurrencyCode {
                return CurrencyCode::fromString($currency);
            })->all();
    }
}
