<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\LoyaltyCard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Phobiavr\PhoberLaravelCommon\Enums\LoyaltyCardStatusEnum;

/**
 * @extends Factory<LoyaltyCard>
 */
class LoyaltyCardFactory extends Factory
{
    protected $model = LoyaltyCard::class;

    public function definition(): array
    {
        return [
            'id' => Customer::factory(),
            'code' => strtoupper($this->faker->bothify('????-####')),
            'status' => LoyaltyCardStatusEnum::BASIC->value,
        ];
    }
}
