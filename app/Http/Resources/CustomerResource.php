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
            "full_name"  => $this->full_name,
            "status"     => $this->status,
            "birthday"   => $this->birthday,
            "gender"     => $this->gender,
            "note"       => $this->note,
            "contacts"   => ContactResource::collection($this->whenLoaded('contacts')),

            "days_until_birthday" => $this->days_until_birthday,
        ];
    }
}
