<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\LoyaltyCard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Phobiavr\PhoberLaravelCommon\Pageable\PageableRequest;

class CustomerService {
    public function search(PageableRequest $request, ?string $trim): LengthAwarePaginator {
        $query = Customer::query();

        if ($trim) {
            $search = '%' . addcslashes($trim, '\\%_') . '%';

            $query->where(function (Builder $q) use ($search) {
                $q->orWhere('first_name', 'LIKE', $search)
                    ->orWhere('last_name', 'LIKE', $search)
                    ->orWhere('note', 'LIKE', $search)
                    ->orWhereHas('contacts', fn(Builder $b) => $b->where('value', 'LIKE', $search))
                    ->orWhereHas('loyaltyCard', fn(Builder $b) => $b->where('code', 'LIKE', $search));
            });
        }

        return $query->paginateFromRequest($request);
    }

    /**
     * @param Contact[] $contacts
     */
    public function create(array $customerData, array $contacts): Customer {
        return DB::transaction(function () use ($customerData, $contacts) {
            $customer = Customer::create($customerData);

            if (!empty($contacts)) {
                $customer->contacts()->saveMany($contacts);
            }

            return $customer->load('contacts');
        });
    }

    /**
     * @param Contact[] $contacts
     */
    public function update(int $id, array $customerData, array $contacts): Customer {
        return DB::transaction(function () use ($id, $customerData, $contacts) {
            $customer = Customer::findOrFail($id);

            $customer->update($customerData);

            $customer->contacts()->delete();
            $customer->contacts()->saveMany($contacts);

            return $customer->load('contacts');
        });
    }

    public function setLoyaltyCard(int $customerId, string $code, string $status): Customer {
        $customer = Customer::findOrFail($customerId);

        $card         = LoyaltyCard::firstOrNew(['id' => $customer->id]);
        $card->id     = $customer->id;
        $card->code   = $code;
        $card->status = $status;
        $card->save();

        return $customer->fresh(['contacts', 'loyaltyCard']);
    }

    public function find(int $id): Customer {
        return Customer::findOrFail($id);
    }

    public function upcomingBirthdays(int $limit = 5): Collection {
        return Customer::all()
            ->sortBy('days_until_birthday')
            ->values()
            ->take($limit);
    }
}
