<?php

namespace App\Providers;

use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Repositories\Interfaces\ProjectCategoryRepositoryInterface;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Repositories\Interfaces\ServiceCategoryRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectCategoryRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceCategoryRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ProjectRepositoryInterface::class,
            ProjectRepository::class
        );
        $this->app->bind(
            ProjectCategoryRepositoryInterface::class,
            ProjectCategoryRepository::class
        );

        $this->app->bind(
            ServiceRepositoryInterface::class,
            ServiceRepository::class
        );
        $this->app->bind(
            ServiceCategoryRepositoryInterface::class,
            ServiceCategoryRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
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