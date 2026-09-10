<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\PaymentGatewayInterface;
use App\DTO\PaymentResult;
use App\Services\PaymentGatewayResolver;
use Mockery;
use Tests\TestCase;

final class PaymentApiTest extends TestCase
{
    public function testProcessPaymentSuccess(): void
    {
        $gatewayMock = Mockery::mock(PaymentGatewayInterface::class);
        $gatewayMock->shouldReceive('charge')
            ->once()
            ->with(100.0, 'USD')
            ->andReturn(new PaymentResult(
                success: true,
                transactionId: 'txn_1234567890',
                errorMessage: null,
            ));

        $resolverMock = Mockery::mock(PaymentGatewayResolver::class);
        $resolverMock->shouldReceive('resolve')
            ->once()
            ->with('stripe')    
            ->andReturn($gatewayMock);

        $this->app->instance(PaymentGatewayResolver::class, $resolverMock);

        $response = $this->postJson('/api/payments/process', [
            'amount' => 100.0,
            'currency' => 'USD',
            'provider' => 'stripe',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'transaction_id' => 'txn_1234567890',
            ]);
    }

    public function testValidatesProcessPaymentRequest(): void
    {
        $response = $this->postJson('/api/payments/process', [
            'provider' => 'unsupported_gateway',
            'amount' => 0.1,
            'currency' => 'US',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider', 'amount', 'currency']);
    }
}