<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\LoyaltyCard;
use Carbon\Carbon;
use Phobiavr\PhoberLaravelCommon\Enums\LoyaltyCardStatusEnum;
use Phobiavr\PhoberLaravelCommon\Testing\ClearsExistingRows;
use Tests\TestCase;

class CustomerEndpointsTest extends TestCase
{
    use ClearsExistingRows;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearExistingRows(Customer::class, Contact::class, LoyaltyCard::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_rejects_customer_routes_without_a_valid_auth_server_token(): void
    {
        $this->withToken('bad')->getJson('/customers')->assertStatus(401);
    }

    public function test_paginates_and_searches_customers(): void
    {
        $this->authorizeAuthServer();

        Customer::factory()->create(['first_name' => 'Findme']);
        Customer::factory()->create(['first_name' => 'Other']);

        $this->withToken('token')->getJson('/customers?trim=Findme')
            ->assertOk()
            ->assertJsonPath('total', 1);
    }

    public function test_searches_by_name_note_contact_value_and_loyalty_card_code(): void
    {
        $this->authorizeAuthServer();

        $byName = Customer::factory()->create(['first_name' => 'Zendaya', 'last_name' => 'Smith']);
        $byNote = Customer::factory()->create(['note' => 'zendaya fan club member']);
        $byContact = Customer::factory()->create();
        Contact::factory()->for($byContact)->create(['value' => 'zendaya@example.test']);
        $byCard = Customer::factory()->create();
        LoyaltyCard::factory()->create(['id' => $byCard->id, 'code' => 'ZENDAYA-1']);
        Customer::factory()->create(['first_name' => 'Unrelated']);

        $response = $this->withToken('token')->getJson('/customers?trim=zendaya')->assertOk();

        $this->assertEqualsCanonicalizing(
            [$byName->id, $byNote->id, $byContact->id, $byCard->id],
            collect($response->json('data'))->pluck('id')->all()
        );
    }

    public function test_creates_a_customer_with_nested_contacts(): void
    {
        $this->authorizeAuthServer();

        $response = $this->withToken('token')->postJson('/customers', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'birthday' => '1990-01-01',
            'contacts' => [['type' => 'PHONE', 'value' => '555-1234']],
        ]);

        $response->assertOk()
            ->assertJsonPath('first_name', 'Jane')
            ->assertJsonPath('full_name', 'Jane Doe')
            ->assertJsonCount(1, 'contacts');
    }

    public function test_validates_customer_creation_input(): void
    {
        $this->authorizeAuthServer();

        $this->withToken('token')->postJson('/customers', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'birthday']);
    }

    public function test_updates_a_customer_replacing_its_contacts(): void
    {
        $this->authorizeAuthServer();

        $customer = Customer::factory()->create();
        Contact::factory()->for($customer)->create(['value' => 'old-contact']);

        $response = $this->withToken('token')->putJson("/customers/{$customer->id}", [
            'first_name' => 'Renamed',
            'last_name' => $customer->last_name,
            'birthday' => (string) $customer->birthday,
            'contacts' => [['type' => 'EMAIL', 'value' => 'new-contact']],
        ]);

        $response->assertOk()
            ->assertJsonPath('first_name', 'Renamed')
            ->assertJsonCount(1, 'contacts')
            ->assertJsonPath('contacts.0.value', 'new-contact');
    }

    public function test_sets_a_loyalty_card_on_a_customer(): void
    {
        $this->authorizeAuthServer();

        $customer = Customer::factory()->create();

        $this->withToken('token')->putJson("/customers/{$customer->id}/loyalty-card", ['code' => 'ABCD-1'])
            ->assertOk()
            ->assertJsonPath('loyalty_card.code', 'ABCD-1')
            ->assertJsonPath('loyalty_card.status', 'BASIC');
    }

    public function test_updates_the_existing_loyalty_card_in_place_rather_than_duplicating_it(): void
    {
        $this->authorizeAuthServer();

        $customer = Customer::factory()->create();

        $this->withToken('token')->putJson("/customers/{$customer->id}/loyalty-card", [
            'code' => 'FIRST-CODE',
            'status' => LoyaltyCardStatusEnum::BASIC->value,
        ])->assertOk();

        $response = $this->withToken('token')->putJson("/customers/{$customer->id}/loyalty-card", [
            'code' => 'SECOND-CODE',
            'status' => LoyaltyCardStatusEnum::GOLD->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('loyalty_card.code', 'SECOND-CODE')
            ->assertJsonPath('loyalty_card.status', LoyaltyCardStatusEnum::GOLD->value);
        $this->assertSame(1, LoyaltyCard::where('id', $customer->id)->count());
    }

    public function test_lists_upcoming_birthdays_as_a_plain_array_not_the_paginated_shape(): void
    {
        $this->authorizeAuthServer();

        $response = $this->withToken('token')->getJson('/customers/upcoming-birthdays')->assertOk();

        $this->assertTrue(array_is_list($response->json()));
    }

    public function test_shows_a_single_customer_via_the_private_service_to_service_route(): void
    {
        $customer = Customer::factory()->create();

        $this->withHeaders(['X-Service-Secret' => config('service.secret')])
            ->getJson("/customers/{$customer->id}")
            ->assertOk()
            ->assertJsonPath('id', $customer->id);
    }

    public function test_rejects_the_private_customer_route_without_the_service_secret(): void
    {
        $customer = Customer::factory()->create();

        $this->getJson("/customers/{$customer->id}")->assertStatus(401);
    }

    public function test_returns_404_for_a_missing_customer_on_the_private_route(): void
    {
        $this->withHeaders(['X-Service-Secret' => config('service.secret')])
            ->getJson('/customers/999999')
            ->assertStatus(404);
    }
}
