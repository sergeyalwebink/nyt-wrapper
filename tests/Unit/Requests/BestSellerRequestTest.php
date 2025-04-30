<?php

namespace Tests\Unit\Requests;

use App\Exceptions\ApiRequestValidationException;
use App\Http\Requests\BestSellerRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Generator;

class BestSellerRequestTest extends TestCase
{
    #[DataProvider('validDataProvider')]
    public function testValidDataPassesValidation(array $data): void
    {
        $request = new BestSellerRequest([], $data);
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    #[DataProvider('invalidDataProvider')]
    public function testInvalidDataFailsValidationAndThrowsException(array $data, string $expectedField): void
    {
        $this->expectException(ApiRequestValidationException::class);

        $request = new BestSellerRequest([], $data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        if ($validator->fails()) {
            throw new ApiRequestValidationException($validator);
        }
    }

    public static function validDataProvider(): Generator
    {
        yield 'fullValidInput' => [[
            'author' => 'Jane Austen',
            'isbn' => [
                'isbn10' => '123456789X',
                'isbn13' => '9781234567897',
            ],
            'title' => 'Pride and Prejudice',
            'offset' => 10,
        ]];

        yield 'minimalValidInput' => [[
            'title' => 'Short Title',
        ]];

        yield 'onlyIsbn10Valid' => [[
            'isbn' => [
                'isbn10' => '123456789X',
            ],
        ]];
    }

    public static function invalidDataProvider(): Generator
    {
        yield 'invalidIsbn10' => [
            ['isbn' => ['isbn10' => 'invalid-isbn']], 'isbn.isbn10'
        ];

        yield 'invalidIsbn13' => [
            ['isbn' => ['isbn13' => 'bad-isbn']], 'isbn.isbn13'
        ];

        yield 'authorNotString' => [
            ['author' => ['array-instead-of-string']], 'author'
        ];

        yield 'offsetNotInteger' => [
            ['offset' => 'not-an-integer'], 'offset'
        ];

        yield 'titleTooLong' => [
            ['title' => str_repeat('A', 256)], 'title'
        ];
    }
}
