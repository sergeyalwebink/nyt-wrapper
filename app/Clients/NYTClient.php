<?php

namespace App\Clients;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use App\Exceptions\NYTAPIException;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class NYTClient
{
    protected int $timeout = 5;
    protected int $retryTimes = 3;
    protected int $retrySleep = 100; // milliseconds

    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.nyt.base_url');
        $this->apiKey = config('services.nyt.api_key');
    }

    /**
     * @throws NYTAPIException
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('get', $endpoint, $params);
    }

    /**
     * @throws NYTAPIException
     */
    public function post(string $endpoint, array $params = []): array
    {
        return $this->request('post', $endpoint, $params);
    }

    /**
     * @throws NYTAPIException
     */
    private function request(string $method, string $endpoint, array $params): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retryTimes, $this->retrySleep)
                ->$method($this->baseUrl . $endpoint, array_merge($params, ['api-key' => $this->apiKey]));

            return $this->processResponse($response);
        } catch (ConnectionException|RequestException $e) {
            throw new NYTAPIException("Failed to reach NYT API: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws NYTAPIException
     */
    private function processResponse(Response $response): array
    {
        if (!$response->successful()) {
            throw new NYTAPIException("NYT API request failed with status {$response->status()}: {$response->body()}");
        }

        $data = $response->json();

        if (!isset($data['results'])) {
            throw new NYTAPIException("Malformed NYT API response: missing 'results' key.");
        }

        return $data;
    }
}
