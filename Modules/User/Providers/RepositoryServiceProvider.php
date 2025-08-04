<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Modules\User\Repositories\Contracts\RoleRepositoryInterface;
use Modules\User\Repositories\Contracts\PermissionRepositoryInterface;
use Modules\User\Repositories\Eloquent\UserRepository;
use Modules\User\Repositories\Eloquent\RoleRepository;
use Modules\User\Repositories\Eloquent\PermissionRepository;

/**
 * RepositoryServiceProvider
 * 
 * Purpose: Register repository interface bindings for dependency injection
 * Part of: Clean Controller → Repository → Database architecture
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings.
     *
     * @return void
     */
    public function register()
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 