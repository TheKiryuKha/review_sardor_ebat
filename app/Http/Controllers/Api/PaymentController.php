<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Services\PaymentGatewayResolver;
use Illuminate\Http\JsonResponse;

final class PaymentController extends Controller
{
    public function __construct(private readonly PaymentGatewayResolver $resolver)
    {
    }

    public function process(ProcessPaymentRequest $request): JsonResponse
    {
        /** @var string $provider */
        $provider = $request->validated('provider');

        /** @var float|numeric-string $amount */
        $amount = $request->validated('amount');

        /** @var string $currency */
        $currency = $request->validated('currency');

        $gateway = $this->resolver->resolve($provider);

        $result = $gateway->charge((float) $amount, $currency);

        if (! $result->success) {
            return response()->json([
                'success' => false,
                'error' => $result->errorMessage,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'transaction_id' => $result->transactionId,
        ]);
    }
}
