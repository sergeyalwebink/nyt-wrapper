<?php

namespace App\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiRequestValidationException extends Exception
{
    protected Validator $validator;

    public function __construct(Validator $validator)
    {
        parent::__construct("Validation Failed", Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->validator = $validator;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->validator->errors(),
        ], $this->getCode());
    }
}
