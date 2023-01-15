<?php

namespace App\ValueObjects;

use InvalidArgumentException;

final class CurrencyCode
{
    private function __construct(
        public readonly string $code
    ) {
    }

    public static function fromString(string $code): self
    {
        if (ctype_alpha($code) === false) {
            throw new InvalidArgumentException('code must only be alpha characters.');
        }

        if (strlen($code) !== 3) {
            throw new InvalidArgumentException('code must be 3 alpha characters.');
        }

        return new self(strtoupper($code));
    }
}
