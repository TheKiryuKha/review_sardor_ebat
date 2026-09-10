<?php 

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'in:stripe, paypal'],
            'amount' => ['required', 'numeric', 'min:0.5'],
            'currency' => ['required', 'string', 'size:3'],
        ];
    }
}