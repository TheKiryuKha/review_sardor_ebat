<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Services\PaymentGatewayResolver;
use Illuminate\Http\JsonResponse;

final class PaymentController extends Controller
{
    // btw, ты мог сделать final readonly class PaymentController 
    // и тогда бы тебе не пришлось писать тут readonly 
    //
    // (да так бы не полуилост сделать из-за того что ты наследуешься от контроллер, но можно было от него не наследоваться)
    // ты все равно никаких его методов не используешь afaics
    public function __construct(private readonly PaymentGatewayResolver $resolver)
    {
    }

    public function process(ProcessPaymentRequest $request): JsonResponse
    {
        // за type hitns респект
        /** @var string $provider */
        $provider = $request->validated('provider');

        /** @var float|numeric-string $amount */
        $amount = $request->validated('amount');

        /** @var string $currency */
        $currency = $request->validated('currency');

        // nit: $currency, $amount and $provider можно было поместить в 1 dto
        // и сделать кастомный метод $reqeust->toDto() который обернул бы в себя ->validated() и маппинг

        // ==== nit ==== 
        $gateway = $this->resolver->resolve($provider);
        
        $result = $gateway->charge((float) $amount, $currency);
        // ==== nit ====  
        // nit:
        // лично Я бы сделал ProcessPaymentAction->handle($dto)
        // который внутри себя уже вызывал эти два метода. Тогда бизнес логика всего в 1 строку будет

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
