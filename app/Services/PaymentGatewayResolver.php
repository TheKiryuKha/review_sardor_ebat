<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

// nit: final readonly
// nit: PaymentGatewayFactory, хотя resolver тоже норм. Дело вкуса короче
class PaymentGatewayResolver
{
    public function resolve(string $gatewayName): PaymentGatewayInterface
    {
        $gatewayClass = match (strtolower($gatewayName)) {
            'stripe' => StripePaymentGateway::class,
            'paypal' => PaypalPaymentGateway::class,
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gatewayName}"),
        };

        return app()->make($gatewayClass);
    }
}