<?php

namespace App\Http\Requests\Customer;

use Phobiavr\PhoberLaravelCommon\Pageable\PageableRequest;

class SearchRequest extends PageableRequest {
    public function rules(): array {
        return [
            'trim' => 'nullable|string',
        ];
    }

    public function trim(): ?string {
        return $this->input('trim');
    }
}
