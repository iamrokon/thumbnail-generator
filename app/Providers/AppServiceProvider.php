<?php

namespace App\Providers;

use App\Services\Contracts\ImageProcessingServiceInterface;
use App\Services\NodeJsImageProcessingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImageProcessingServiceInterface::class, NodeJsImageProcessingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
