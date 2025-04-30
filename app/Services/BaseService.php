<?php

namespace App\Services;

use App\Clients\NYTClient;
use App\Exceptions\NYTAPIException;

abstract class BaseService
{
    protected NYTClient $client;

    protected string $errorMessage = 'Error making API call';

    public function __construct(NYTClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throws NYTAPIException
     */
    public function makeRequest(string $url, array $query): array
    {
        try {
            $response = $this->client->get($url, $query);

            return $response['results'] ?? [];
        } catch (NYTAPIException $e) {
            throw new NYTAPIException($this->errorMessage, 0);
        }
    }

    abstract protected function buildQuery(array $filters): array;
}
