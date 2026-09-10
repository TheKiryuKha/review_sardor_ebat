<?php

declare(strict_types=1);

namespace App\DTO;

readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public ?string $transactionId = null,
        public ?string $errorMessage = null,
    ) {
    }
}