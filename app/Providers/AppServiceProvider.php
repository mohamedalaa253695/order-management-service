<?php

namespace App\Providers;

use App\Interfaces\Api\V1\AuthServiceInterface;
use App\Interfaces\Api\V1\OrderServiceInterface;
use App\Interfaces\Api\V1\PaymentServiceInterface;
use App\Services\Api\V1\AuthService;
use App\Services\Api\V1\OrderService;
use App\Services\Api\V1\PaymentService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::hashClientSecrets();
        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        //
    }
}
