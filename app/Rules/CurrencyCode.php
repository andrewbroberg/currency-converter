<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use InvalidArgumentException;
use TypeError;

class CurrencyCode implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  string  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail): void
    {
        try {
            \App\ValueObjects\CurrencyCode::fromString($value);
        } catch (InvalidArgumentException|TypeError) {
            $fail("The $attribute is not a valid currency code");
        }
    }
}
