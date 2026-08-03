<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $status
 * @property string $birthday
 * @property string|null $gender
 * @property string|null $note
 * @property \App\Models\LoyaltyCard|null $loyaltyCard
 * @property int $days_until_birthday
 */
class CustomerResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>|Arrayable<array-key, mixed>|\JsonSerializable
     */
    public function toArray($request) {
        $loyaltyCard = $this->loyaltyCard;
        $loyaltyCardData = null;

        if ($loyaltyCard) {
            $loyaltyCardData = [
                'code'   => $loyaltyCard->code,
                'status' => $loyaltyCard->status,
            ];
        }

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

            "loyalty_card" => $this->when($loyaltyCardData !== null, fn() => $loyaltyCardData),

            "days_until_birthday" => $this->days_until_birthday,
        ];
    }
}
