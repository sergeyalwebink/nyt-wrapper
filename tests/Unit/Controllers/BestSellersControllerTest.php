<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\BestSellersController;
use App\Services\BestSellersService;
use App\Http\Requests\BestSellerRequest;
use App\Exceptions\NYTAPIException;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class BestSellersControllerTest extends TestCase
{
    protected BestSellersService $bestSellersService;
    protected BestSellersController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bestSellersService = Mockery::mock(BestSellersService::class);
        $this->controller = new BestSellersController($this->bestSellersService);
    }

    /**
     * @throws NYTAPIException
     */
    public function testHistoryReturnsJsonResponse(): void
    {
        $filters = ['author' => 'Jane Austen', 'title' => 'Pride and Prejudice'];
        $bestSellers = [['title' => 'Pride and Prejudice', 'author' => 'Jane Austen']];

        $this->bestSellersService->shouldReceive('request')
            ->with($filters)
            ->once()
            ->andReturn($bestSellers);

        $request = Mockery::mock(BestSellerRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($filters);

        $response = $this->controller->history($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($bestSellers, $response->getData(true));
    }

    public function testHistoryThrowsNYTAPIException(): void
    {
        $filters = ['author' => 'Jane Austen'];

        $this->bestSellersService->shouldReceive('request')
            ->with($filters)
            ->once()
            ->andThrow(NYTAPIException::class);

        $request = Mockery::mock(BestSellerRequest::class);
        $request->shouldReceive('validated')->once()->andReturn($filters);

        $this->expectException(NYTAPIException::class);

        $this->controller->history($request);
    }
}
