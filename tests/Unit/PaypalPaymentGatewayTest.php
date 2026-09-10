<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\PaypalPaymentGateway;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class PaypalPaymentGatewayTest extends TestCase
{
    public function test_charge_succeeds_with_string_amount_and_json(): void
    {
        // again, http запросы можно было вынести в отдельный класс с интерфейсом и мокать его в тестах
        Http::fake([
            'https://api.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'id' => 'PAYPAL_ORDER_123',
                'status' => 'COMPLETED',
            ], 200),
        ]);

        $gateway = new PaypalPaymentGateway('paypal_access_token');
        $result = $gateway->charge(25.00, 'EUR');

        $this->assertTrue($result->success);
        $this->assertSame('PAYPAL_ORDER_123', $result->transactionId);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.sandbox.paypal.com/v2/checkout/orders'
                && $request->hasHeader('Authorization', 'Bearer paypal_access_token')
                && $request['amount'] === '25'
                && $request['currency'] === 'eur';
        });
    }

    public function test_charge_handles_paypal_error(): void
    {
        Http::fake([
            'https://api.sandbox.paypal.com/v2/checkout/orders' => Http::response([
                'error' => ['message' => 'ORDER_NOT_APPROVED'],
            ], 422),
        ]);

        $gateway = new PaypalPaymentGateway('paypal_access_token');
        $result = $gateway->charge(25.00, 'EUR');

        $this->assertFalse($result->success);
        $this->assertSame('ORDER_NOT_APPROVED', $result->errorMessage);
    }
}