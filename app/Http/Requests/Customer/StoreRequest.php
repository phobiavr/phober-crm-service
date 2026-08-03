<?php

namespace App\Http\Requests\Customer;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest {
    /** @return array<string, string> */
    public function rules(): array {
        return [
            'first_name'       => 'required|string',
            'last_name'        => 'required|string',
            'birthday'         => 'required|date',
            'gender'           => 'nullable|string|in:M,F',
            'note'             => 'nullable|string',
            'contacts'         => 'array|sometimes',
            'contacts.*.type'  => 'required_with:contacts',
            'contacts.*.value' => 'required_with:contacts',
        ];
    }

    /**
     * Customer attributes only — without nested contacts.
     *
     * @return array<string, mixed>
     */
    public function customerData(): array {
        return $this->safe()->except('contacts');
    }

    /**
     * @return Contact[]
     */
    public function contactModels(): array {
        return array_map(
            fn(array $contact) => new Contact($contact),
            $this->safe()->input('contacts', []),
        );
    }
}
