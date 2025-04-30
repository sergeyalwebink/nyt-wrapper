<?php

namespace App\Http\Requests;

use App\Exceptions\ApiRequestValidationException;
use App\Rules\Isbn;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BestSellerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|array',
            'isbn.isbn10' => [new Isbn()],
            'isbn.isbn13' => [new Isbn()],
            'title' => 'nullable|string|max:255',
            'offset' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.isbn10' => 'The provided ISBN-10 is invalid.',
            'isbn.isbn13' => 'The provided ISBN-13 is invalid.',
            'author.string' => 'Author should be a string.',
            'offset.integer' => 'Offset should be an integer.',
        ];
    }


    /**
     * @throws ApiRequestValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ApiRequestValidationException($validator);
    }
}
