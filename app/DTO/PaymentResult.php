<?php

declare(strict_types=1);

namespace App\DTO;

// круто
readonly class PaymentResult
{
    public function __construct(
        // если в будущем будут еще статусы помимо failed/success это будет сложно расширять
        // лучше сразу сделать PaymentStatus enum
        public bool $success,
        public ?string $transactionId = null,
        public ?string $errorMessage = null,
    ) {
    }
}