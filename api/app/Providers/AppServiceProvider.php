<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ClockInterface;
use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\PasswordHasherInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Application\Ports\Outbound\SalesReportQueryInterface;
use App\Application\Ports\Outbound\TokenGeneratorInterface;
use App\Application\Ports\Outbound\UnitOfWorkInterface;
use App\Application\Ports\Outbound\UserRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\DatabaseSalesReportQuery;
use App\Infrastructure\Persistence\Repositories\DatabaseUnitOfWork;
use App\Infrastructure\Persistence\Repositories\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Repositories\EloquentProductRepository;
use App\Infrastructure\Persistence\Repositories\EloquentSaleRepository;
use App\Infrastructure\Persistence\Repositories\EloquentUserRepository;
use App\Infrastructure\Services\Argon2PasswordHasher;
use App\Infrastructure\Services\JwtTokenGenerator;
use App\Infrastructure\Services\LocalFileStorage;
use App\Infrastructure\Services\SystemClock;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ClockInterface::class, SystemClock::class);
        $this->app->singleton(PasswordHasherInterface::class, Argon2PasswordHasher::class);
        $this->app->singleton(FileStorageInterface::class, LocalFileStorage::class);
        $this->app->singleton(TokenGeneratorInterface::class, JwtTokenGenerator::class);
        $this->app->singleton(UnitOfWorkInterface::class, DatabaseUnitOfWork::class);

        $this->app->singleton(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->singleton(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->singleton(SaleRepositoryInterface::class, EloquentSaleRepository::class);
        $this->app->singleton(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->singleton(SalesReportQueryInterface::class, DatabaseSalesReportQuery::class);
    }

    public function boot(): void
    {
    }
}
