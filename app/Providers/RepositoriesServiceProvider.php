<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Test;
use App\Models\Framework;
use App\Models\Frameworkdetails;
use App\Models\Supplier;
use App\Models\Attendance;
use App\Models\Attendanceview;
use App\Models\Staff;
use App\Models\Contractorder;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\TestRepository;
use App\Repositories\Contracts\FrameworkRepository;
use App\Repositories\Contracts\FrameworkdetailsRepository;
use App\Repositories\Contracts\SupplierRepository;
use App\Repositories\Contracts\AttendanceRepository;
use App\Repositories\Contracts\AttendanceviewRepository;
use App\Repositories\Contracts\StaffRepository;
use App\Repositories\Contracts\ContractorderRepository;
use App\Repositories\EloquentUserRepository;
use App\Repositories\EloquentTestRepository;
use App\Repositories\EloquentFrameworkRepository;
use App\Repositories\EloquentFrameworkdetailsRepository;
use App\Repositories\EloquentSupplierRepository;
use App\Repositories\EloquentAttendanceRepository;
use App\Repositories\EloquentAttendanceviewRepository;
use App\Repositories\EloquentStaffRepository;
use App\Repositories\EloquentContractorderRepository;

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
        $this->app->bind(FrameworkRepository::class, function () {
            return new EloquentFrameworkRepository(new Framework());
        });
        $this->app->bind(FrameworkdetailsRepository::class, function () {
            return new EloquentFrameworkdetailsRepository(new Frameworkdetails());
        });
        $this->app->bind(SupplierRepository::class, function () {
            return new EloquentSupplierRepository(new Supplier());
        });
        $this->app->bind(AttendanceRepository::class, function () {
            return new EloquentAttendanceRepository(new Attendance());
        });
        $this->app->bind(AttendanceviewRepository::class, function () {
            return new EloquentAttendanceviewRepository(new Attendanceview());
        });
        $this->app->bind(StaffRepository::class, function () {
            return new EloquentStaffRepository(new Staff());
        });
        $this->app->bind(ContractorderRepository::class, function () {
            return new EloquentContractorderRepository(new Contractorder());
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
            FrameworkRepository::class,
            FrameworkdetailsRepository::class,
            SupplierRepository::class,
            AttendanceRepository::class,
            AttendanceviewRepository::class,
            Staff::class,
            Contractorder::class
        ];
    }
}
