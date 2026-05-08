<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Phobiavr\PhoberLaravelCommon\Enums\LoyaltyCardStatusEnum;

class LoyaltyCardRequest extends FormRequest {
    public function rules(): array {
        return [
            'code'   => 'required|string',
            'status' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void {
        if (!$this->filled('status')) {
            $this->merge(['status' => LoyaltyCardStatusEnum::BASIC->value]);
        }
    }

    public function code(): string {
        return $this->input('code');
    }

    public function status(): string {
        return $this->input('status');
    }
}
