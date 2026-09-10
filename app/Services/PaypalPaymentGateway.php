<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\DTO\PaymentResult;
use Illuminate\Support\Facades\Http;

// охуенный класс; не добавить не убавить
final readonly class PaypalPaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private string $apiKey,
    ) {
    }

    public function charge(float $amount, string $currency): PaymentResult
    {
        $response = Http::asJson()->withToken($this->apiKey)
            ->post('https://api.sandbox.paypal.com/v2/checkout/orders', [
                'amount' => (string) $amount,
                'currency' => strtolower($currency),
                'source' => 'paypal',
            ]);

            if ($response->successful()) {
                return new PaymentResult(
                    success: true,
                    transactionId: (string) $response->json('id'),
                );
            }
            $rawMessage = $response->json('error.message');

            return new PaymentResult(
                success: false,
                errorMessage: is_string($rawMessage) ? $rawMessage : 'An error occurred while processing the payment.',
            );
    }
}