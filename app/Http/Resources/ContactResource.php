<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $type
 * @property string $value
 */
class ContactResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>|Arrayable<array-key, mixed>|\JsonSerializable
     */
    public function toArray($request) {
        return [
            "type"  => $this->type,
            "value" => $this->value,
        ];
    }
}
