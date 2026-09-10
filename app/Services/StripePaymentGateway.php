<?php

declare(strict_types=1);

namespace App\Services;
use App\Contracts\PaymentGatewayInterface;
use App\DTO\PaymentResult;
use Illuminate\Support\Facades\Http;

final readonly class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct(
        private string $apiKey,
    ) {
    }

    public function charge(float $amount, string $currency): PaymentResult
    {
       $response = Http::asForm()->withToken($this->apiKey)
       ->post('https://api.stripe.com/v1/charges', [
            'amount' => (int) ($amount * 100), 
            'currency' => strtolower($currency),
            'source' => 'tok_visa',
        ]);

        if ($response->successful()) {
            return new PaymentResult(
                success: true,
                transactionId: (string) $response->json('id'),
            );
        }
        
        $rawMessage = $response->json('error.message');
        $errorMessage = is_string($rawMessage) ? $rawMessage : 'Payment failed';

return new PaymentResult(
    success: false,
    errorMessage: $errorMessage,
);
    }
}