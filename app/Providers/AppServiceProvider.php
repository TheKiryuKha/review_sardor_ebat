<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\StripePaymentGateway;
use App\Services\PaypalPaymentGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(StripePaymentGateway::class, function (): StripePaymentGateway {

            $stripeKey = (string) config('services.stripe.secret');

            return new StripePaymentGateway($stripeKey);
        });

        $this->app->bind(PaypalPaymentGateway::class, function (): PaypalPaymentGateway {

            $paypalKey = (string) config('services.paypal.secret');

            return new PaypalPaymentGateway($paypalKey);
        });
    }

    public function boot(): void
    {
        //
    }

}