<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Isbn implements Rule
{
    /**
     * Determine if the given value is valid ISBN.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $isbn = preg_replace('/\D/', '', $value);

        if ($attribute === 'isbn.isbn10') {
            return strlen($value) === 10;
        }

        if ($attribute === 'isbn.isbn13') {
            return strlen($value) === 13;
        }
        
        dump();

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute is not a valid ISBN number.';
    }
}
