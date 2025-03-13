<?php

namespace App\Providers;

use App\Interfaces\AdminUserRepositoryInterface;
use App\Interfaces\ProfessorLogsRepositoryInterface;
use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Interfaces\QrCodeRepositoryInterface;
use App\Interfaces\QuarterRepositoryInterface;
use App\Repositories\AdminUserRepository;
use App\Repositories\ProfessorLogsRepository;
use App\Repositories\ProfessorMilkBookingRepository;
use App\Repositories\ProfessorRepository;
use App\Repositories\ProfessorTransactionRepository;
use App\Repositories\QrCodeRepository;
use App\Repositories\QuarterRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AdminUserRepositoryInterface::class, AdminUserRepository::class);
        $this->app->bind(ProfessorRepositoryInterface::class, ProfessorRepository::class);
        $this->app->bind(ProfessorLogsRepositoryInterface::class, ProfessorLogsRepository::class);
        $this->app->bind(ProfessorTransactionRepositoryInterface::class, ProfessorTransactionRepository::class);
        $this->app->bind(QuarterRepositoryInterface::class, QuarterRepository::class);
        $this->app->bind(QrCodeRepositoryInterface::class, QrCodeRepository::class);
        $this->app->bind(ProfessorMilkBookingRepositoryInterface::class, ProfessorMilkBookingRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
