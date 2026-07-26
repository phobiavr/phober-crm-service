<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Phobiavr\PhoberLaravelCommon\Enums\ContactTypeEnum;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'type' => ContactTypeEnum::PHONE->value,
            'value' => $this->faker->phoneNumber(),
        ];
    }
}
