<?php

namespace App\Services;

use App\Exceptions\NYTAPIException;
use App\Interfaces\NYTServiceInterface;

class BestSellersService extends BaseService implements NYTServiceInterface
{
    protected string $errorMessage = 'Error fetching best sellers history';

    /**
     * @throws NYTAPIException
     */
    public function request(array $filters): array
    {
        $query = $this->buildQuery($filters);

        return $this->makeRequest('/svc/books/v3/lists/best-sellers/history.json', $query);
    }

    protected function buildQuery(array $filters): array
    {
        return collect($filters)
            ->filter(fn ($value) => !empty($value))
            ->tap(function (&$filtered) {
                if (isset($filtered['isbn']) && $filtered['isbn']) {
                    $filtered['isbn'] = implode(';', $filtered['isbn']);
                }
            })
            ->only(['author', 'isbn', 'title', 'offset'])
            ->toArray();
    }
}
