<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

final class LslPurchaseIntentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar_id' => ['required', 'uuid'],
            'product_sku' => ['required', 'string', 'max:64'],
            'quantity' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'in:L$'],
            'amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
