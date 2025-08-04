<?php

namespace Modules\Shipper\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use Modules\Shipper\Repositories\Contracts\CarrierRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\CarrierConfigurationRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\QuoteRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\LabelRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\TrackingRepositoryInterface;
use Modules\Shipper\Repositories\Contracts\ApiLogRepositoryInterface;

// Repository Implementations
use Modules\Shipper\Repositories\Eloquent\CarrierRepository;
use Modules\Shipper\Repositories\Eloquent\CarrierConfigurationRepository;
use Modules\Shipper\Repositories\Eloquent\QuoteRepository;
use Modules\Shipper\Repositories\Eloquent\LabelRepository;
use Modules\Shipper\Repositories\Eloquent\TrackingRepository;
use Modules\Shipper\Repositories\Eloquent\ApiLogRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(CarrierRepositoryInterface::class, CarrierRepository::class);
        $this->app->bind(CarrierConfigurationRepositoryInterface::class, CarrierConfigurationRepository::class);
        $this->app->bind(QuoteRepositoryInterface::class, QuoteRepository::class);
        $this->app->bind(LabelRepositoryInterface::class, LabelRepository::class);
        $this->app->bind(TrackingRepositoryInterface::class, TrackingRepository::class);
        $this->app->bind(ApiLogRepositoryInterface::class, ApiLogRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 