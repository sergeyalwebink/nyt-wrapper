<?php

namespace Tests\Unit\Clients;

use App\Clients\NytClient;
use App\Exceptions\NYTAPIException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Generator;

class NYTClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.nyt.base_url' => 'https://api.nytimes.com']);
        config(['services.nyt.api_key' => 'test-api-key']);
    }

    public function testGetSuccessfulResponse(): void
    {
        Http::fake([
            'https://api.nytimes.com/svc/test-endpoint*' => Http::response(['results' => ['data']], 200),
        ]);

        $client = new NytClient();
        $response = $client->get('/svc/test-endpoint', ['param' => 'value']);

        $this->assertEquals(['results' => ['data']], $response);
    }

    public function testPostSuccessfulResponse(): void
    {
        Http::fake([
            'https://api.nytimes.com/svc/test-endpoint*' => Http::response(['status' => 'ok', 'results' => []], 200),
        ]);

        $client = new NytClient();
        $response = $client->post('/svc/test-endpoint', ['foo' => 'bar']);

        $this->assertEquals(
            [
                'status' => 'ok',
                'results' => [],
            ],
            $response
        );
    }

    #[DataProvider('failedRequestProvider')]
    public function testFailedResponsesThrowException(string $method, string $endpoint, string $body, int $status): void
    {
        Http::fake([
            "https://api.nytimes.com$endpoint*" => Http::response($body, $status),
        ]);

        $client = new NytClient();

        $this->expectException(NYTAPIException::class);
        $this->expectExceptionMessage($status);

        $client->$method($endpoint);
    }

    public static function failedRequestProvider(): Generator
    {
        yield ['get', '/svc/fail-get', 'Unauthorized', 401];
        yield ['post', '/svc/fail-post', 'Internal Error', 500];
    }
}
