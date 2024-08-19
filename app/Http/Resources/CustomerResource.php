<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request) {
        return [
            "id"         => $this->id,
            "first_name" => $this->first_name,
            "last_name"  => $this->last_name,
            "status"     => $this->status ?: 'pending',
            "birthday"   => $this->birthday,
            "gender"     => $this->gender ?: 'M',
            "note"       => $this->note,
            "contacts"   => ContactResource::collection($this->whenLoaded('contacts')),
        ];
    }
}
