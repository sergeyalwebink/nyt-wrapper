<?php

namespace App\Providers;

use App\Clients\NYTClient;
use App\Http\Middleware\NYTAPIMiddleware;
use App\Services\BestSellersService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NYTClient::class);
        $this->app->bind(BestSellersService::class);
    }

    public function boot(): void
    {
        Route::prefix('api')
            ->middleware(NYTAPIMiddleware::class)
            ->group(base_path('routes/api.php'));
    }
}
