<?php

namespace Tests\Unit\Services;

use App\Services\BestSellersService;
use App\Clients\NYTClient;
use App\Exceptions\NYTAPIException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;
use Generator;

class BestSellersServiceTest extends TestCase
{
    protected BestSellersService $bestSellersService;
    protected NYTClient $nytClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->nytClient = $this->createMock(NYTClient::class);
        $this->bestSellersService = new BestSellersService($this->nytClient);
    }

    /**
     * @throws NYTAPIException
     */
    #[DataProvider('getBestSellersHistoryDataProvider')]
    public function testGetBestSellersHistory(array $filters, array $expectedResults): void
    {
        $this->nytClient->method('get')
            ->willReturn(['results' => $expectedResults]);

        $response = $this->bestSellersService->request($filters);

        $this->assertEquals($expectedResults, $response);
    }

    #[DataProvider('getBestSellersHistoryFailureDataProvider')]
    public function testGetBestSellersHistoryThrowsException(array $filters, string $expectedExceptionMessage): void
    {
        $this->nytClient->method('get')
            ->willThrowException(new NYTAPIException('Mocked API failure'));

        $this->expectException(NYTAPIException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->bestSellersService->request($filters);
    }

    public static function getBestSellersHistoryDataProvider(): Generator
    {
        yield 'valid filters' => [
            ['author' => 'John Doe', 'title' => 'Some Book', 'isbn' => ['1234567890']],
            [['title' => 'Some Book', 'author' => 'John Doe', 'isbn' => '1234567890']],
        ];

        yield 'empty filters' => [
            [],
            [],
        ];

        yield 'filter with empty values' => [
            ['author' => '', 'isbn' => []],
            [],
        ];

        yield 'filter with valid values' => [
            ['author' => 'Jane Austen', 'isbn' => ['123456789X'], 'title' => 'Pride and Prejudice', 'offset' => 10],
            [['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'isbn' => '123456789X', 'offset' => 10]],
        ];
    }

    public static function getBestSellersHistoryFailureDataProvider(): Generator
    {
        yield 'NYTAPIExceptionThrown' => [
            ['author' => 'Some Author'],
            'Error fetching best sellers history',
        ];
    }
}
