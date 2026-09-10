<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\StripePaymentGateway;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class StripePaymentGatewayTest extends TestCase
{
    public function test_charge_succeeds_and_converts_amount_to_cents(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/charges' => Http::response([
                'id' => 'ch_123456789',
                'status' => 'succeeded',
            ], 200),
        ]);

        $gateway = new StripePaymentGateway('sk_test_123');
        $result = $gateway->charge(10.50, 'USD');

        $this->assertTrue($result->success);
        $this->assertSame('ch_123456789', $result->transactionId);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.stripe.com/v1/charges'
                && $request->hasHeader('Authorization', 'Bearer sk_test_123')
                && $request['amount'] === 1050
                && $request['currency'] === 'usd';
        });
    }

    public function test_charge_handles_stripe_error(): void
    {
        Http::fake([
            'https://api.stripe.com/v1/charges' => Http::response([
                'error' => ['message' => 'Card was declined'],
            ], 402),
        ]);

        $gateway = new StripePaymentGateway('sk_test_123');
        $result = $gateway->charge(10.50, 'USD');

        $this->assertFalse($result->success);
        $this->assertSame('Card was declined', $result->errorMessage);
    }
}