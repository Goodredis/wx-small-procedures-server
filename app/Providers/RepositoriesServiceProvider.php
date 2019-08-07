<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Test;
use App\Models\Framwork;
use App\Models\Framworkdetails;
use App\Models\Supplier;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\TestRepository;
use App\Repositories\Contracts\FramworkRepository;
use App\Repositories\Contracts\FramworkdetailsRepository;
use App\Repositories\Contracts\SupplierRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentTestRepository;
use App\Repositories\EloquentFramworkRepository;
use App\Repositories\EloquentFramworkdetailsRepository;
use App\Repositories\EloquentSupplierRepository;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepository::class, function () {
            return new EloquentUserRepository(new User());
        });
        $this->app->bind(TestRepository::class, function () {
            return new EloquentTestRepository(new Test());
        });
        $this->app->bind(FramworkRepository::class, function () {
            return new EloquentFramworkRepository(new Framwork());
        });
        $this->app->bind(FramworkdetailsRepository::class, function () {
            return new EloquentFramworkdetailsRepository(new Framworkdetails());
        });
        $this->app->bind(SupplierRepository::class, function () {
            return new EloquentSupplierRepository(new Supplier());
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            UserRepository::class,
            TestRepository::class,
            FramworkRepository::class,
            FramworkdetailsRepository::class,
            SupplierRepository::class
        ];
    }
}
