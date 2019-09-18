<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\User;
use App\Models\Test;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\EloquentOrderRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\TestRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentTestRepository;

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
        $this->app->bind(OrderRepository::class, function () {
           return new EloquentOrderRepository(new Order());
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
            OrderRepository::class
        ];
    }
}
