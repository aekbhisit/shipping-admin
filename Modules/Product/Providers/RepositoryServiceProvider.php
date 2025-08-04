<?php

namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use Modules\Product\Repositories\Eloquent\ProductRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Product Repository
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ProductRepositoryInterface::class,
        ];
    }
} 