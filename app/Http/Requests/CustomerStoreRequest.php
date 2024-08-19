<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStoreRequest extends FormRequest {
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array {
        return [
            'first_name'       => 'required|string',
            'last_name'        => 'required|string',
            'birthday'         => 'required|date',
            'gender'           => 'nullable|char',
            'note'             => 'nullable|string',
            'contacts'         => 'array|sometimes',
            'contacts.*.type'  => 'required_with:contacts',
            'contacts.*.value' => 'required_with:contacts',
        ];
    }
}
