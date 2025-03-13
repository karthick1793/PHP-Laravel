<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use App\Interfaces\ProfessorMilkBookingRepositoryInterface;
use App\Interfaces\ProfessorRepositoryInterface;
use App\Interfaces\ProfessorTransactionRepositoryInterface;
use App\Services\EncryptDecryptService;
use App\Services\JWTService;
use App\Services\MilkBookingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->resolving(Controller::class, function ($controller, $app) {
            $controller->jWTService = $app->make(JWTService::class);
        });

        $this->app->resolving(EncryptDecryptService::class, function ($service, $app) {
            $key = env('AES_SECRET_KEY');
            $service->setAesImageSecretKey($key);
        });

        $this->app->resolving(MilkBookingService::class, function ($service, $app) {
            $service->professorMilkBookingRepository = $app->make(ProfessorMilkBookingRepositoryInterface::class);
            $service->professorTransactionRepository = $app->make(ProfessorTransactionRepositoryInterface::class);
            $service->professorRepository = $app->make(ProfessorRepositoryInterface::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
