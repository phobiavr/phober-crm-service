<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Customer;
use App\Models\LoyaltyCard;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Phobiavr\PhoberLaravelCommon\Pageable\PageableRequest;

class CustomerService {
    /** @return LengthAwarePaginator<int, Customer> */
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
     * @param array<string, mixed> $customerData
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
     * @param array<string, mixed> $customerData
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

        /** @var int<0, max> $customerModelId */
        $customerModelId = $customer->id;

        $card         = LoyaltyCard::firstOrNew(['id' => $customerModelId]);
        $card->id     = $customerModelId;
        $card->code   = $code;
        $card->status = $status;
        $card->save();

        return $customer->fresh(['contacts', 'loyaltyCard']) ?? $customer;
    }

    public function find(int $id): Customer {
        return Customer::findOrFail($id);
    }

    /** @return Collection<int, Customer> */
    public function upcomingBirthdays(int $limit = 5): Collection {
        $todayKey = (int) Carbon::today()->format('md');

        $results = $this->birthdaysFrom($todayKey, '>=', $limit);

        if ($results->count() < $limit) {
            $results = $results->concat(
                $this->birthdaysFrom($todayKey, '<', $limit - $results->count())
            );
        }

        return $results->values();
    }

    /** @return Collection<int, Customer> */
    private function birthdaysFrom(int $todayKey, string $operator, int $limit): Collection {
        $results = Customer::query()
            ->without(['contacts', 'loyaltyCard'])
            ->where('birthday_month_day', $operator, $todayKey)
            ->orderBy('birthday_month_day')
            ->limit($limit)
            ->get();

        $boundary = $results->last()?->birthday_month_day;

        if ($results->count() === $limit && $boundary !== null) {
            $ties = Customer::query()
                ->without(['contacts', 'loyaltyCard'])
                ->where('birthday_month_day', $boundary)
                ->whereNotIn('id', $results->pluck('id'))
                ->get();

            $results = $results->concat($ties);
        }

        return $results;
    }
}
