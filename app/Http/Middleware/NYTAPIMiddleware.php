<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use JsonException;
use Illuminate\Http\JsonResponse;

class NYTAPIMiddleware
{
    /**
     * @throws JsonException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $cacheKey = 'nyt_api_request_' . md5($request->fullUrl() . json_encode($request->all(), JSON_THROW_ON_ERROR));

        Log::info('Checking cache for NYT API', [
            'url' => $request->fullUrl(),
            'params' => $request->all()
        ]);

        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            return new JsonResponse(
                json_decode($cachedResponse['content'], true, 512, JSON_THROW_ON_ERROR),
                $cachedResponse['status'],
                $cachedResponse['headers']
            );
        }

        /** @var JsonResponse $response */
        $response = $next($request);

        Cache::put($cacheKey, [
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
        ], 60);

        return $response;
    }
}
