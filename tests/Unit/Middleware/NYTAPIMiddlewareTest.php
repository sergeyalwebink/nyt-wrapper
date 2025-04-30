<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\NYTAPIMiddleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use JsonException;

class NYTAPIMiddlewareTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testMiddlewareCachesTheResponse(): void
    {
        Cache::shouldReceive('get')->once()->andReturn(null);
        Cache::shouldReceive('put')->once();

        $middleware = new NytApiMiddleware();

        $request = Request::create('/api/test', 'GET', ['query' => 'Laravel']);

        $response = new JsonResponse(['data' => 'test'], 200, ['X-Test-Header' => 'HeaderValue']);

        $next = function () use ($response) {
            return $response;
        };

        $result = $middleware->handle($request, $next);

        $this->assertEquals(200, $result->status());
        $this->assertEquals(['data' => 'test'], $result->getData(true));
        $this->assertEquals('HeaderValue', $result->headers->get('X-Test-Header'));
    }

    /**
     * @throws JsonException
     */
    public function testMiddlewareReturnsCachedResponse(): void
    {
        $cachedData = [
            'content' => json_encode(['data' => 'cached'], JSON_THROW_ON_ERROR),
            'status' => 200,
            'headers' => ['X-Test-Header' => ['CachedValue']],
        ];

        Cache::shouldReceive('get')->once()->andReturn($cachedData);

        $middleware = new NytApiMiddleware();

        $request = Request::create('/api/test', 'GET', ['query' => 'Laravel']);

        $next = function () {
            $this->fail('Next closure should not be called if cache is hit.');
        };

        $result = $middleware->handle($request, $next);

        $this->assertEquals(200, $result->status());
        $this->assertEquals(['data' => 'cached'], $result->getData(true));
        $this->assertEquals('CachedValue', $result->headers->get('X-Test-Header'));
    }
}
