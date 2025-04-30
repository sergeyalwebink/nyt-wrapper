<?php

namespace App\Http\Controllers;

use App\Exceptions\NYTAPIException;
use App\Http\Requests\BestSellerRequest;
use App\Services\BestSellersService;
use Illuminate\Http\JsonResponse;

class BestSellersController
{
    protected BestSellersService $bestSellersService;

    public function __construct(BestSellersService $bestSellersService)
    {
        $this->bestSellersService = $bestSellersService;
    }

    /**
     * @throws NYTAPIException
     */
    public function history(BestSellerRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $bestSellers = $this->bestSellersService->request($filters);

        return response()->json($bestSellers);
    }
}
