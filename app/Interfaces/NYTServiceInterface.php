<?php

namespace App\Interfaces;

interface NYTServiceInterface
{
    public function request(array $filters): array;
}
