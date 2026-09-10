<?php 

declare(strict_types=1);

namespace App\Contracts;

use App\DTO\PaymentResult;

// БЛЯЯЯЯЯЯЯ ИНТЕРФЕЙСЫ, какой же секс >~<
interface PaymentGatewayInterface
{
    public function charge(float $amount, string $currency): PaymentResult;
}